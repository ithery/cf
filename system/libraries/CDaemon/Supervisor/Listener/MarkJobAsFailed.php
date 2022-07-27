<?php

class CDaemon_Supervisor_Listener_MarkJobAsFailed {
    /**
     * The job repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_JobRepositoryInterface
     */
    public $jobs;

    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct() {
        $this->jobs = CDaemon_Supervisor::jobRepository();
    }

    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobFailed $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobFailed $event) {
        $this->jobs->failed(
            $event->exception,
            $event->connectionName,
            $event->queue,
            $event->payload
        );
    }
}
