<?php

class CBackup_Exception_InvalidCommandException extends Exception {
    public static function create($reason) {
        return new static($reason);
    }
}
