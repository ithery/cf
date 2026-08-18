<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Event_Looping {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The queue name.
     *
     * @var string
     */
    public $queue;

    /**
     * The worker options.
     *
     * @var null|CQueue_WorkerOptions
     */
    public $options;

    /**
     * Create a new event instance.
     *
     * @param string                    $connectionName
     * @param string                    $queue
     * @param null|CQueue_WorkerOptions $options
     *
     * @return void
     */
    public function __construct($connectionName, $queue, $options = null) {
        $this->queue = $queue;
        $this->connectionName = $connectionName;
        $this->options = $options;
    }
}
