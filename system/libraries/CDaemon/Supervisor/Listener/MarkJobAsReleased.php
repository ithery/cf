<?php

class CDaemon_Supervisor_Listener_MarkJobAsReleased {
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
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobReleased $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobReleased $event) {
        $this->jobs->released($event->connectionName, $event->queue, $event->payload);
    }
}
