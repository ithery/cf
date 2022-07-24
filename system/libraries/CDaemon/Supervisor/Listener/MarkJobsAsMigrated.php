<?php

class CDaemon_Supervisor_Listener_MarkJobsAsMigrated {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_JobsMigrated $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_JobsMigrated $event) {
        CDaemon_Supervisor::jobRepository()->migrated($event->connectionName, $event->queue, $event->payloads);
    }
}
