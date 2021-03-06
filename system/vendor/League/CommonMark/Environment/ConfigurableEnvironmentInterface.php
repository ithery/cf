<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Environment;

use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

/**
 * Interface for an Environment which can be configured with config settings, parsers, processors, and renderers
 */
interface ConfigurableEnvironmentInterface extends EnvironmentInterface {
    /**
     * @param array<string, mixed> $config
     */
    public function mergeConfig(array $config);

    /**
     * Registers the given extension with the Environment
     */
    public function addExtension(ExtensionInterface $extension);

    /**
     * Registers the given block start parser with the Environment
     *
     * @param BlockStartParserInterface $parser   Block parser instance
     * @param int                       $priority Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addBlockStartParser(BlockStartParserInterface $parser, $priority = 0);

    /**
     * Registers the given inline parser with the Environment
     *
     * @param InlineParserInterface $parser   Inline parser instance
     * @param int                   $priority Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addInlineParser(InlineParserInterface $parser, $priority = 0);

    /**
     * Registers the given delimiter processor with the Environment
     *
     * @param DelimiterProcessorInterface $processor Delimiter processors instance
     */
    public function addDelimiterProcessor(DelimiterProcessorInterface $processor);

    /**
     * Registers the given node renderer with the Environment
     *
     * @param string                $nodeClass The fully-qualified node element class name the renderer below should handle
     * @param NodeRendererInterface $renderer  The renderer responsible for rendering the type of element given above
     * @param int                   $priority  Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addRenderer($nodeClass, NodeRendererInterface $renderer, $priority = 0);

    /**
     * Registers the given event listener
     *
     * @param string   $eventClass Fully-qualified class name of the event this listener should respond to
     * @param callable $listener   Listener to be executed
     * @param int      $priority   Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addEventListener($eventClass, callable $listener, $priority = 0);
}
