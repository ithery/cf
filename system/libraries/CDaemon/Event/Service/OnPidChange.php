<?php

class CDaemon_Event_Service_OnPidChange extends CDaemon_Event_ServiceEventAbstract {
    /**
     * @var int
     */
    public $oldPid;

    /**
     * @var int
     */
    public $newPid;

    /**
     * CDaemon_Event_Service_OnPidChange constructor.
     *
     * @param CDaemon_ServiceAbstract $service
     * @param int                     $oldPid
     * @param int                     $newPid
     */
    public function __construct(CDaemon_ServiceAbstract $service, $oldPid, $newPid) {
        $this->service = $service;
        $this->oldPid = $oldPid;
        $this->newPid = $newPid;
    }

    /**
     * Get the new PID.
     *
     * @return int
     */
    public function getNewPid() {
        return $this->newPid;
    }

    /**
     * Get the old PID.
     *
     * @return int
     */
    public function getOldPid() {
        return $this->oldPid;
    }
}
