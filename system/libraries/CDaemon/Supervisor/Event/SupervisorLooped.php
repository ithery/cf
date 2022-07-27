<?php

class CDaemon_Supervisor_Event_SupervisorLooped {
    /**
     * The supervisor instance.
     *
     * @var \CDaemon_Supervisor_Supervisor
     */
    public $supervisor;

    /**
     * Create a new event instance.
     *
     * @param \CDaemon_Supervisor_Supervisor $supervisor
     *
     * @return void
     */
    public function __construct(CDaemon_Supervisor_Supervisor $supervisor) {
        $this->supervisor = $supervisor;
    }
}
