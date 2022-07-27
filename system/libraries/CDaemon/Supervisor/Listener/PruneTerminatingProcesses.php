<?php

class CDaemon_Supervisor_Listener_PruneTerminatingProcesses {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_SupervisorLooped $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_SupervisorLooped $event) {
        $event->supervisor->pruneTerminatingProcesses();
    }
}
