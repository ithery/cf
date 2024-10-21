<?php

abstract class CBackup_Monitor_AbstractHealthCheck {
    abstract public function checkHealth(CBackup_BackupDestination $backupDestination);

    public function name() {
        return cstr::title(c::classBasename($this));
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
