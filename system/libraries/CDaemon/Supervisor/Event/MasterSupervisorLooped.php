<?php

class CDaemon_Supervisor_Event_MasterSupervisorLooped {
    /**
     * The master supervisor instance.
     *
     * @var \CDaemon_Supervisor_MasterSupervisor
     */
    public $master;

    /**
     * Create a new event instance.
     *
     * @param \CDaemon_Supervisor_MasterSupervisor $master
     *
     * @return void
     */
    public function __construct(CDaemon_Supervisor_MasterSupervisor $master) {
        $this->master = $master;
    }
}
