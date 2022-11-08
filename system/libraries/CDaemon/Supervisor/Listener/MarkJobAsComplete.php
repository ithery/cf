<?php

class CDaemon_Supervisor_Listener_MarkJobAsComplete {
    /**
     * The job repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_JobRepositoryInterface
     */
    public $jobs;

    /**
     * The tag repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_TagRepositoryInterface
     */
    public $tags;

    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct() {
        $this->jobs = CDaemon_Supervisor::jobRepository();
        $this->tags = CDaemon_Supervisor::tagRepository();
    }

    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobDeleted $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobDeleted $event) {
        $this->jobs->completed($event->payload, $event->job->hasFailed());

        if (!$event->job->hasFailed() && count($this->tags->monitored($event->payload->tags())) > 0) {
            $this->jobs->remember($event->connectionName, $event->queue, $event->payload);
        }
    }
}
