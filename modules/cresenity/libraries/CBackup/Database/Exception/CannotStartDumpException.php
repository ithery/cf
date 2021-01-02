<?php

class CBackup_Database_Exception_CannotStartDumpException extends Exception {
    /**
     * @param string $name
     *
     * @return CDatabase_Database_Exception_CannotStartDumpException
     */
    public static function emptyParameter($name) {
        return new static("Parameter `{$name}` cannot be empty.");
    }
}
