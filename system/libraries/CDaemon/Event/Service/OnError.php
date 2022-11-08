<?php

class CDaemon_Event_Service_OnError {
    protected $service;

    protected $message;

    public function __construct(CDaemon_ServiceAbstract $service, $message) {
        $this->service = $service;
        $this->message = $message;
    }
}
