<?php

class CDaemon_Supervisor_Listener_ExpireSupervisors {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_MasterSupervisorLooped $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_MasterSupervisorLooped $event) {
        CDaemon_Supervisor::masterSupervisorRepository()->flushExpired();

        CDaemon_Supervisor::supervisorRepository()->flushExpired();
    }
}
