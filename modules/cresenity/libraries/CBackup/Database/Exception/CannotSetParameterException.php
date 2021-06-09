<?php

class CBackup_Database_Exception_CannotSetParameterException extends Exception {
    /**
     * @param string $name
     * @param string $conflictName
     *
     * @return CDatabase_Database_Exception_CannotSetParameterException
     */
    public static function conflictingParameters($name, $conflictName) {
        return new static("Cannot set `{$name}` because it conflicts with parameter `{$conflictName}`.");
    }
}
