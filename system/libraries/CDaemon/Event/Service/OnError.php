<?php

class CDaemon_Event_Service_OnError extends CDaemon_Event_ServiceEventAbstract {
    public $message;

    public $label;

    public function __construct(CDaemon_ServiceAbstract $service, $message, $label) {
        $this->service = $service;
        $this->message = $message;
        $this->label = $label;
    }

    public function getErrorMessage() {
        return $this->message;
    }

    public function getLabel() {
        return $this->label;
    }
}
