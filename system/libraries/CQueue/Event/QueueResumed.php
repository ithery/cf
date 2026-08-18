<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Raised when a paused queue is resumed.
 */
class CQueue_Event_QueueResumed {
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
     * @param string $connectionName
     * @param string $queue
     *
     * @return void
     */
    public function __construct($connectionName, $queue) {
        $this->connectionName = $connectionName;
        $this->queue = $queue;
    }
}
