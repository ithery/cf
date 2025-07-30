<?php

class CDaemon_Event_Service_OnPidChange extends CDaemon_Event_ServiceEventAbstract {
    public $oldPid;

    public $newPid;

    public function __construct(CDaemon_ServiceAbstract $service, $oldPid, $newPid) {
        $this->service = $service;
        $this->oldPid = $oldPid;
        $this->newPid = $newPid;
    }

    public function getNewPid() {
        return $this->newPid;
    }

    public function getOldPid() {
        return $this->oldPid;
    }
}
