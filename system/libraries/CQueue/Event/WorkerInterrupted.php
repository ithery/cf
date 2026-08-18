<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Raised when the worker is asked to quit by SIGQUIT, SIGTERM or SIGINT.
 */
class CQueue_Event_WorkerInterrupted {
    /**
     * The signal that interrupted the worker.
     *
     * @var int
     */
    public $signal;

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
     * @param int                       $signal
     * @param null|string               $connectionName
     * @param null|string               $queue
     * @param null|CQueue_WorkerOptions $options
     *
     * @return void
     */
    public function __construct($signal, $connectionName = null, $queue = null, $options = null) {
        $this->signal = $signal;
        $this->connectionName = $connectionName;
        $this->queue = $queue;
        $this->options = $options;
    }
}
