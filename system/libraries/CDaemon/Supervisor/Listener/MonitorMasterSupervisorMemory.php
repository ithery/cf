<?php

class CDaemon_Supervisor_Listener_MonitorMasterSupervisorMemory {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_MasterSupervisorLooped $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_MasterSupervisorLooped $event) {
        $master = $event->master;

        if ($master->memoryUsage() > CF::config('daemon.supervisor.memory_limit', 64)) {
            $master->terminate(12);
        }
    }
}
