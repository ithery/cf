<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CBackup_Monitor_AbstractHealthCheck {

    abstract public function checkHealth(CBackup_BackupDestination $backupDestination);

    public function name() {
        return cstr::title(class_basename($this));
    }

    protected function fail($message) {
        throw CBackup_Monitor_Exception_InvalidHealthCheckException::because($message);
    }

    protected function failIf($condition, $message) {
        if ($condition) {
            $this->fail($message);
        }
    }

    protected function failUnless($condition, $message) {
        if (!$condition) {
            $this->fail($message);
        }
    }

}
