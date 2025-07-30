<?php

class CDaemon_Event_Service_OnSignal extends CDaemon_Event_ServiceEventAbstract {
    public $signal;

    public function __construct(CDaemon_ServiceAbstract $service, $signal) {
        $this->service = $service;
        $this->signal = $signal;
    }

    public function getSignal() {
        return $this->signal;
    }
}
