<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Raised when a queue is paused, so workers stop popping from it.
 */
class CQueue_Event_QueuePaused {
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
     * How long the pause lasts, or null when it is indefinite.
     *
     * @var null|DateInterval|DateTime|int
     */
    public $ttl;

    /**
     * @param string                         $connectionName
     * @param string                         $queue
     * @param null|DateInterval|DateTime|int $ttl
     *
     * @return void
     */
    public function __construct($connectionName, $queue, $ttl = null) {
        $this->connectionName = $connectionName;
        $this->queue = $queue;
        $this->ttl = $ttl;
    }
}
