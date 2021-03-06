<?php

class CServer_Dns_Result_TXTResult extends CServer_Dns_Result {
    private $record;

    public function __construct($record) {
        parent::__construct();
        $this->setRecord($record);
    }

    public function setRecord($record) {
        $this->record = $record;
    }

    public function getRecord() {
        return $this->record;
    }
}
