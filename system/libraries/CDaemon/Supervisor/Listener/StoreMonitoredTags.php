<?php

class CDaemon_Supervisor_Listener_StoreMonitoredTags {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobPushed $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobPushed $event) {
        $monitoring = CDaemon_Supervisor::tagRepository()->monitored($event->payload->tags());

        if (!empty($monitoring)) {
            CDaemon_Supervisor::tagRepository()->add($event->payload->id(), $monitoring);
        }
    }
}
