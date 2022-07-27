<?php

class CDaemon_Supervisor_Listener_MonitorSupervisorMemory {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_SupervisorLooped $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_SupervisorLooped $event) {
        $supervisor = $event->supervisor;

        if ($supervisor->memoryUsage() > $supervisor->options->memory) {
            $supervisor->terminate(12);
        }
    }
}
