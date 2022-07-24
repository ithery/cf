<?php

class CDaemon_Supervisor_Event_JobsMigrated {
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
     * The job payloads that were migrated.
     *
     * @var \CCollection
     */
    public $payloads;

    /**
     * Create a new event instance.
     *
     * @param array $payloads
     *
     * @return void
     */
    public function __construct($payloads) {
        $this->payloads = c::collect($payloads)->map(function ($job) {
            return new CDaemon_Supervisor_Queue_JobPayload($job);
        });
    }

    /**
     * Set the connection name.
     *
     * @param string $connectionName
     *
     * @return $this
     */
    public function connection($connectionName) {
        $this->connectionName = $connectionName;

        return $this;
    }

    /**
     * Set the queue name.
     *
     * @param string $queue
     *
     * @return $this
     */
    public function queue($queue) {
        $this->queue = $queue;

        return $this;
    }
}
