<?php

class CServer_Dns_Result_NSResult extends CServer_Dns_Result {
    private $nameserver;

    public function __construct($ns) {
        parent::__construct();
        $this->setNameserver($ns);
    }

    public function setNameserver($server) {
        $this->nameserver = $server;
    }

    public function getNameserver() {
        return $this->nameserver;
    }
}
