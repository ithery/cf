<?php

class CDaemon_Supervisor_Listener_StoreJob {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobPushed $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobPushed $event) {
        CDaemon_Supervisor::jobRepository()->pushed(
            $event->connectionName,
            $event->queue,
            $event->payload
        );
    }
}
