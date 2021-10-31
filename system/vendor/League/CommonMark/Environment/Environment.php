<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Environment;

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Delimiter\DelimiterParser;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorCollection;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Event\ListenerData;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlFilter;
use League\CommonMark\Util\PrioritizedList;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final class Environment implements ConfigurableEnvironmentInterface, ListenerProviderInterface {
    /**
     * @var ExtensionInterface[]
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $extensions = [];

    /**
     * @var ExtensionInterface[]
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $uninitializedExtensions = [];

    /**
     * @var bool
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $extensionsInitialized = false;

    /**
     * @var PrioritizedList<BlockStartParserInterface>
     *
     * @psalm-readonly
     */
    private $blockStartParsers;

    /**
     * @var PrioritizedList<InlineParserInterface>
     *
     * @psalm-readonly
     */
    private $inlineParsers;

    /**
     * @var DelimiterProcessorCollection
     *
     * @psalm-readonly
     */
    private $delimiterProcessors;

    /**
     * @var array<string, PrioritizedList<NodeRendererInterface>>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $renderersByClass = [];

    /**
     * @var PrioritizedList<ListenerData>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $listenerData;

    /**
     * @var EventDispatcherInterface|null
     */
    private $eventDispatcher;

    /**
     * @var Configuration
     *
     * @psalm-readonly
     */
    private $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = []) {
        $this->config = new Configuration($config);

        $this->blockStartParsers = new PrioritizedList();
        $this->inlineParsers = new PrioritizedList();
        $this->listenerData = new PrioritizedList();
        $this->delimiterProcessors = new DelimiterProcessorCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function mergeConfig(array $config) {
        $this->assertUninitialized('Failed to modify configuration.');

        $this->config->merge($config);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig($key, $default = null) {
        return $this->config->get($key, $default);
    }

    public function addBlockStartParser(BlockStartParserInterface $parser, $priority = 0) {
        $this->assertUninitialized('Failed to add block start parser.');

        $this->blockStartParsers->add($parser, $priority);
        $this->injectEnvironmentAndConfigurationIfNeeded($parser);

        return $this;
    }

    public function addInlineParser(InlineParserInterface $parser, $priority = 0) {
        $this->assertUninitialized('Failed to add inline parser.');

        $this->inlineParsers->add($parser, $priority);
        $this->injectEnvironmentAndConfigurationIfNeeded($parser);

        return $this;
    }

    public function addDelimiterProcessor(DelimiterProcessorInterface $processor) {
        $this->assertUninitialized('Failed to add delimiter processor.');
        $this->delimiterProcessors->add($processor);
        $this->injectEnvironmentAndConfigurationIfNeeded($processor);

        return $this;
    }

    public function addRenderer($nodeClass, NodeRendererInterface $renderer, $priority = 0) {
        $this->assertUninitialized('Failed to add renderer.');

        if (!isset($this->renderersByClass[$nodeClass])) {
            $this->renderersByClass[$nodeClass] = new PrioritizedList();
        }

        $this->renderersByClass[$nodeClass]->add($renderer, $priority);
        $this->injectEnvironmentAndConfigurationIfNeeded($renderer);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockStartParsers() {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->blockStartParsers->getIterator();
    }

    public function getDelimiterProcessors() {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->delimiterProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderersForClass($nodeClass) {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        // If renderers are defined for this specific class, return them immediately
        if (isset($this->renderersByClass[$nodeClass])) {
            return $this->renderersByClass[$nodeClass];
        }

        /** @psalm-suppress TypeDoesNotContainType -- Bug: https://github.com/vimeo/psalm/issues/3332 */
        while (\class_exists($parent = isset($parent) ? $parent : $nodeClass) && $parent = \get_parent_class($parent)) {
            if (!isset($this->renderersByClass[$parent])) {
                continue;
            }

            // "Cache" this result to avoid future loops
            return $this->renderersByClass[$nodeClass] = $this->renderersByClass[$parent];
        }

        return [];
    }

    /**
     * Get all registered extensions
     *
     * @return ExtensionInterface[]
     */
    public function getExtensions() {
        return $this->extensions;
    }

    /**
     * Add a single extension
     *
     * @return $this
     */
    public function addExtension(ExtensionInterface $extension) {
        $this->assertUninitialized('Failed to add extension.');

        $this->extensions[] = $extension;
        $this->uninitializedExtensions[] = $extension;

        return $this;
    }

    private function initializeExtensions() {
        // Ask all extensions to register their components
        while (\count($this->uninitializedExtensions) > 0) {
            foreach ($this->uninitializedExtensions as $i => $extension) {
                $extension->register($this);
                unset($this->uninitializedExtensions[$i]);
            }
        }

        $this->extensionsInitialized = true;

        // Create the special delimiter parser if any processors were registered
        if ($this->delimiterProcessors->count() > 0) {
            $this->inlineParsers->add(new DelimiterParser($this->delimiterProcessors), ~PHP_INT_MAX);
        }
    }

    private function injectEnvironmentAndConfigurationIfNeeded($object) {
        if ($object instanceof EnvironmentAwareInterface) {
            $object->setEnvironment($this);
        }

        if ($object instanceof ConfigurationAwareInterface) {
            $object->setConfiguration($this->config);
        }
    }

    public static function createCommonMarkEnvironment() {
        $environment = new static();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->mergeConfig([
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break' => "\n",
            ],
            'html_input' => HtmlFilter::ALLOW,
            'allow_unsafe_links' => true,
            'max_nesting_level' => \PHP_INT_MAX,
        ]);

        return $environment;
    }

    public static function createGFMEnvironment() {
        $environment = self::createCommonMarkEnvironment();
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        return $environment;
    }

    public function addEventListener($eventClass, callable $listener, $priority = 0) {
        $this->assertUninitialized('Failed to add event listener.');

        $this->listenerData->add(new ListenerData($eventClass, $listener), $priority);

        if (\is_object($listener)) {
            $this->injectEnvironmentAndConfigurationIfNeeded($listener);
        } elseif (\is_array($listener) && \is_object($listener[0])) {
            $this->injectEnvironmentAndConfigurationIfNeeded($listener[0]);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch($event) {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        if ($this->eventDispatcher !== null) {
            return $this->eventDispatcher->dispatch($event);
        }

        foreach ($this->getListenersForEvent($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }

            $listener($event);
        }

        return $event;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher) {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable<callable>
     */
    public function getListenersForEvent($event) {
        foreach ($this->listenerData as $listenerData) {
            \assert($listenerData instanceof ListenerData);

            if (!\is_a($event, $listenerData->getEvent())) {
                continue;
            }

            yield function (object $event) use ($listenerData) {
                if (!$this->extensionsInitialized) {
                    $this->initializeExtensions();
                }

                return \call_user_func($listenerData->getListener(), $event);
            };
        }
    }

    /**
     * @return iterable<InlineParserInterface>
     */
    public function getInlineParsers() {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->inlineParsers->getIterator();
    }

    /**
     * @throws \RuntimeException
     *
     * @param mixed $message
     */
    private function assertUninitialized($message) {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException($message . ' Extensions have already been initialized.');
        }
    }
}
