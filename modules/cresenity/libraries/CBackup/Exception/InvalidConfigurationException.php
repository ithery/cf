<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Exception_InvalidConfigurationException extends Exception {

    public static function cannotUseUnsupportedDriver($connectionName, $driverName) {
        return new static("Db connection `{$connectionName}` uses an unsupported driver `{$driverName}`. Only `mysql` and `pgsql` are supported.");
    }

}
