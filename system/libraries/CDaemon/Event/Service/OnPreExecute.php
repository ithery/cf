<?php

class CDaemon_Event_Service_OnPreExecute extends CDaemon_Event_ServiceEventAbstract {
    public $signal;

    public function __construct(CDaemon_ServiceAbstract $service) {
        $this->service = $service;
    }
}
