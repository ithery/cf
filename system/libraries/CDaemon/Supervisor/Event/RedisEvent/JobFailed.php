<?php

class CDaemon_Supervisor_Event_RedisEvent_JobFailed extends CDaemon_Supervisor_Event_RedisEvent {
    /**
     * The exception that caused the failure.
     *
     * @var \Exception
     */
    public $exception;

    /**
     * The queue job instance.
     *
     * @var \CQueue_AbstractJob
     */
    public $job;

    /**
     * Create a new event instance.
     *
     * @param \Exception          $exception
     * @param \CQueue_AbstractJob $job
     * @param string              $payload
     *
     * @return void
     */
    public function __construct($exception, $job, $payload) {
        $this->job = $job;
        $this->exception = $exception;

        parent::__construct($payload);
    }
}
