<?php

defined('SYSPATH') or die('No direct access allowed.');

use DebugBar\DataCollector\Renderable;

use DebugBar\DataCollector\DataCollector;

/**
 * Collects info about exceptions.
 */
class CDebug_DebugBar_DataCollector_ExceptionsCollector extends DataCollector implements Renderable {
    protected $exceptions = [];

    protected $chainExceptions = false;

    /**
     * Adds an exception to be profiled in the debug bar.
     *
     * @param Exception $e
     *
     * @deprecated in favor on addThrowable
     */
    public function addException(Exception $e) {
        $this->addThrowable($e);
    }

    /**
     * Adds a Throwable to be profiled in the debug bar.
     *
     * @param \Throwable $e
     */
    public function addThrowable($e) {
        $this->exceptions[] = $e;
        if ($this->chainExceptions && $previous = $e->getPrevious()) {
            $this->addThrowable($previous);
        }
    }

    /**
     * Configure whether or not all chained exceptions should be shown.
     *
     * @param bool $chainExceptions
     */
    public function setChainExceptions($chainExceptions = true) {
        $this->chainExceptions = $chainExceptions;
    }

    /**
     * Returns the list of exceptions being profiled.
     *
     * @return array[\Throwable]
     */
    public function getExceptions() {
        return $this->exceptions;
    }

    public function collect() {
        return [
            'count' => count($this->exceptions),
            'exceptions' => array_map([$this, 'formatThrowableData'], $this->exceptions)
        ];
    }

    /**
     * Returns exception data as an array.
     *
     * @param Exception $e
     *
     * @return array
     *
     * @deprecated in favor on formatThrowableData
     */
    public function formatExceptionData(Exception $e) {
        return $this->formatThrowableData($e);
    }

    /**
     * Returns Throwable data as an array.
     *
     * @param \Throwable $e
     *
     * @return array
     */
    public function formatThrowableData($e) {
        $filePath = $e->getFile();
        if ($filePath && file_exists($filePath)) {
            $lines = file($filePath);
            $start = $e->getLine() - 4;
            $lines = array_slice($lines, $start < 0 ? 0 : $start, 7);
        } else {
            $lines = ["Cannot open the file ($filePath) in which the exception occurred "];
        }

        return [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $filePath,
            'line' => $e->getLine(),
            'surrounding_lines' => $lines,
            'xdebug_link' => $this->getXdebugLink($filePath, $e->getLine())
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        return 'exceptions';
    }

    /**
     * @return array
     */
    public function getWidgets() {
        return [
            'exceptions' => [
                'icon' => 'bug',
                'widget' => 'PhpDebugBar.Widgets.ExceptionsWidget',
                'map' => 'exceptions.exceptions',
                'default' => '[]'
            ],
            'exceptions:badge' => [
                'map' => 'exceptions.count',
                'default' => 'null'
            ]
        ];
    }
}
