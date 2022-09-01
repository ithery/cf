<?php

class CDaemon_Supervisor_Listener_MarshalFailedEvent {
    /**
     * Handle the event.
     *
     * @param \CQueue_Event_JobFailed $event
     *
     * @return void
     */
    public function handle(CQueue_Event_JobFailed $event) {
        if (!$event->job instanceof CQueue_Job_RedisJob) {
            return;
        }
        CEvent::dispatcher()->dispatch((new CDaemon_Supervisor_Event_RedisEvent_JobFailed(
            $event->exception,
            $event->job,
            $event->job->getReservedJob()
        ))->connection($event->connectionName)->queue($event->job->getQueue()));
    }
}
