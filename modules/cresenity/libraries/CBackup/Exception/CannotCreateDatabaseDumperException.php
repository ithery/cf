<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Exception_CannotCreateDatabaseDumperException extends Exception {

    public static function unsupportedDriver($driver) {
        return new static("Cannot create a dumper for db driver `{$driver}`. Use `mysql`, `pgsql`, `mongodb` or `sqlite`.");
    }

}
