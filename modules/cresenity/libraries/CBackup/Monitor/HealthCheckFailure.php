<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_HealthCheckFailure {

    /** @var \CBackup_Monitor_AbstractHealthCheck */
    protected $healthCheck;

    /** @var \Exception */
    protected $exception;

    public function __construct(CBackup_Monitor_AbstractHealthCheck $healthCheck, Exception $exception) {
        $this->healthCheck = $healthCheck;
        $this->exception = $exception;
    }

    /**
     * 
     * @return CBackup_Monitor_AbstractHealthCheck
     */
    public function healthCheck() {
        return $this->healthCheck;
    }

    public function exception() {
        return $this->exception;
    }

    public function wasUnexpected() {
        return !$this->exception instanceof CBackup_Monitor_Exception_InvalidHealthCheckException;
    }

}
