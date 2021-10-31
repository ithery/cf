<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 3:10:38 PM
 */
use Psr\Log\AbstractLogger;

/**
 * Provides a way to log messages
 */
class CDebug_DataCollector_MessagesCollector extends AbstractLogger implements CDebug_Interface_DataCollectorInterface, CDebug_DataCollector_MessagesAggregateInterface, CDebug_Bar_Interface_RenderableInterface, CDebug_DataCollector_AssetProviderInterface {
    protected $name;
    protected $messages = [];
    protected $aggregates = [];
    protected $dataFormater;
    protected $varDumper;
    // The HTML var dumper requires debug bar users to support the new inline assets, which not all
    // may support yet - so return false by default for now.
    protected $useHtmlVarDumper = true;

    /**
     * @param string $name
     */
    public function __construct($name = 'messages') {
        $this->name = $name;
    }

    /**
     * Sets the data formater instance used by this collector
     *
     * @param DataFormatterInterface $formater
     *
     * @return $this
     */
    public function setDataFormatter(CDebug_Interface_DataFormatterInterface $formater) {
        $this->dataFormater = $formater;
        return $this;
    }

    /**
     * @return DataFormatterInterface
     */
    public function getDataFormatter() {
        if ($this->dataFormater === null) {
            $this->dataFormater = CDebug_DataCollector::getDefaultDataFormatter();
        }
        return $this->dataFormater;
    }

    /**
     * Sets the variable dumper instance used by this collector
     *
     * @param CDebug_DataFormatter_DebugBarVarDumper $varDumper
     *
     * @return $this
     */
    public function setVarDumper(CDebug_DataFormatter_DebugBarVarDumper $varDumper) {
        $this->varDumper = $varDumper;
        return $this;
    }

    /**
     * Gets the variable dumper instance used by this collector
     *
     * @return CDebug_DataFormatter_DebugBarVarDumper
     */
    public function getVarDumper() {
        if ($this->varDumper === null) {
            $this->varDumper = CDebug_DataCollector::getDefaultVarDumper();
        }
        return $this->varDumper;
    }

    /**
     * Sets a flag indicating whether the Symfony HtmlDumper will be used to dump variables for
     * rich variable rendering.  Be sure to set this flag before logging any messages for the
     * first time.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function useHtmlVarDumper($value = true) {
        $this->useHtmlVarDumper = $value;
        return $this;
    }

    /**
     * Indicates whether the Symfony HtmlDumper will be used to dump variables for rich variable
     * rendering.
     *
     * @return mixed
     */
    public function isHtmlVarDumperUsed() {
        return $this->useHtmlVarDumper;
    }

    /**
     * Adds a message
     *
     * A message can be anything from an object to a string
     *
     * @param mixed  $message
     * @param string $label
     * @param mixed  $isString
     */
    public function addMessage($message, $label = 'info', $isString = true) {
        $messageText = $message;
        $messageHtml = null;
        if (!is_string($message)) {
            // Send both text and HTML representations; the text version is used for searches
            $messageText = $this->getDataFormatter()->formatVar($message);
            if ($this->isHtmlVarDumperUsed()) {
                $messageHtml = $this->getVarDumper()->renderVar($message);
            }
            $isString = false;
        }
        $this->messages[] = [
            'message' => $messageText,
            'message_html' => $messageHtml,
            'is_string' => $isString,
            'label' => $label,
            'time' => microtime(true)
        ];
    }

    /**
     * Aggregates messages from other collectors
     *
     * @param CDebug_DataCollector_MessagesAggregateInterface $messages
     */
    public function aggregate(CDebug_DataCollector_MessagesAggregateInterface $messages) {
        $this->aggregates[] = $messages;
    }

    /**
     * @return array
     */
    public function getMessages() {
        $messages = $this->messages;
        foreach ($this->aggregates as $collector) {
            $msgs = array_map(function ($m) use ($collector) {
                $m['collector'] = $collector->getName();
                return $m;
            }, $collector->getMessages());
            $messages = array_merge($messages, $msgs);
        }
        // sort messages by their timestamp
        usort($messages, function ($a, $b) {
            if ($a['time'] === $b['time']) {
                return 0;
            }
            return $a['time'] < $b['time'] ? -1 : 1;
        });
        return $messages;
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     */
    public function log($level, $message, array $context = []) {
        $this->addMessage($message, $level);
    }

    /**
     * Deletes all messages
     */
    public function clear() {
        $this->messages = [];
    }

    /**
     * @return array
     */
    public function collect() {
        $messages = $this->getMessages();
        return [
            'count' => count($messages),
            'messages' => $messages
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAssets() {
        return $this->isHtmlVarDumperUsed() ? $this->getVarDumper()->getAssets() : [];
    }

    /**
     * @return array
     */
    public function getWidgets() {
        $name = $this->getName();
        return [
            "$name" => [
                'icon' => 'list-alt',
                'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
                'map' => "$name.messages",
                'default' => '[]'
            ],
            "$name:badge" => [
                'map' => "$name.count",
                'default' => 'null'
            ]
        ];
    }
}
