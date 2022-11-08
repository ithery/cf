<?php

class CDaemon_Supervisor_Listener_MarkJobAsReserved {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobReserved $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobReserved $event) {
        CDaemon_Supervisor::jobRepository()->reserved($event->connectionName, $event->queue, $event->payload);
    }
}
