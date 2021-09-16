<?php

class CBackup_Monitor_Exception_InvalidHealthCheckException extends Exception {
    public static function because($message) {
        return new static($message);
    }
}
