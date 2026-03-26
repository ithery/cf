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
     * Create a new event instance.
     *
     * @param string $connectionName
     * @param string $queue
     *
     * @return void
     */
    public function __construct($connectionName, $queue) {
        $this->queue = $queue;
        $this->connectionName = $connectionName;
    }
}
