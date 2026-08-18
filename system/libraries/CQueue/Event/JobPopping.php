<?php

class CQueue_Event_JobPopping {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The queue name.
     *
     * @var null|string
     */
    public $queue;

    /**
     * Create a new event instance.
     *
     * @param string      $connectionName
     * @param null|string $queue
     *
     * @return void
     */
    public function __construct($connectionName, $queue = null) {
        $this->connectionName = $connectionName;
        $this->queue = $queue;
    }
}
