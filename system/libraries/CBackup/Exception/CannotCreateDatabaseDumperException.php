<?php

class CBackup_Exception_CannotCreateDatabaseDumperException extends Exception {
    public static function unsupportedDriver($driver) {
        return new static("Cannot create a dumper for db driver `{$driver}`. Use `mysql`, `pgsql`, `mongodb` or `sqlite`.");
    }
}
