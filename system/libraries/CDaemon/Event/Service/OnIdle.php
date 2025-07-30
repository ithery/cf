<?php

class CDaemon_Event_Service_OnIdle extends CDaemon_Event_ServiceEventAbstract {
    public function __construct(CDaemon_ServiceAbstract $service) {
        $this->service = $service;
    }
}
