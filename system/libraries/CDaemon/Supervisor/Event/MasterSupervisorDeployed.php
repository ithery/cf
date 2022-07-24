<?php

class CDaemon_Supervisor_Event_MasterSupervisorDeployed {
    /**
     * The master supervisor that was deployed.
     *
     * @var string
     */
    public $master;

    /**
     * Create a new event instance.
     *
     * @param string $master
     *
     * @return void
     */
    public function __construct($master) {
        $this->master = $master;
    }
}
