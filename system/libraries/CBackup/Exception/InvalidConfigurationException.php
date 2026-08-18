<?php

class CBackup_Exception_InvalidConfigurationException extends Exception {
    public static function cannotUseUnsupportedDriver($connectionName, $driverName) {
        return new static("Db connection `{$connectionName}` uses an unsupported driver `{$driverName}`. Only `mysql` and `pgsql` are supported.");
    }
}
