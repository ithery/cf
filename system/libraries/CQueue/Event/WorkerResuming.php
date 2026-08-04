<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Raised when the worker is resumed by SIGCONT.
 */
class CQueue_Event_WorkerResuming {
    /**
     * The connection name.
     *
     * @var null|string
     */
    public $connectionName;

    /**
     * The queue name.
     *
     * @var null|string
     */
    public $queue;

    /**
     * The worker options.
     *
     * @var null|CQueue_WorkerOptions
     */
    public $options;

    /**
     * @param null|string               $connectionName
     * @param null|string               $queue
     * @param null|CQueue_WorkerOptions $options
     *
     * @return void
     */
    public function __construct($connectionName = null, $queue = null, $options = null) {
        $this->connectionName = $connectionName;
        $this->queue = $queue;
        $this->options = $options;
    }
}
