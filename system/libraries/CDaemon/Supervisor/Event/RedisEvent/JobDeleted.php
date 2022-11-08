<?php

class CDaemon_Supervisor_Event_RedisEvent_JobDeleted extends CDaemon_Supervisor_Event_RedisEvent {
    /**
     * The queue job instance.
     *
     * @var CQueue_AbstractJob
     */
    public $job;

    /**
     * Create a new event instance.
     *
     * @param \CQueue_AbstractJob $job
     * @param string              $payload
     *
     * @return void
     */
    public function __construct($job, $payload) {
        $this->job = $job;

        parent::__construct($payload);
    }
}
