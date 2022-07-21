<?php

class CBackup_Exception_CannotStartDumpException extends Exception {
    /**
     * @param string $name
     *
     * @return static
     */
    public static function emptyParameter($name) {
        return new static("Parameter `{$name}` cannot be empty.");
    }
}
