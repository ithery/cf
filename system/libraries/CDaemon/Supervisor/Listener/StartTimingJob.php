<?php

class CDaemon_Supervisor_Listener_StartTimingJob {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobReserved $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobReserved $event) {
        CDaemon::log('StartTiming');
        CDaemon_Supervisor::stopwatch()->start($event->payload->id());
    }
}
