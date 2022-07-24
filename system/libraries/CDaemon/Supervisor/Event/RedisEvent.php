<?php

class CDaemon_Supervisor_Event_RedisEvent {
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
     * The job payload.
     *
     * @var CDaemon_Supervisor_Queue_JobPayload
     */
    public $payload;

    /**
     * Create a new event instance.
     *
     * @param string $payload
     *
     * @return void
     */
    public function __construct($payload) {
        $this->payload = new CDaemon_Supervisor_Queue_JobPayload($payload);
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
