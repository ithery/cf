<?php

class CBackup_Exception_InvalidBackupDestinationException extends Exception {
    public static function diskNotSet($backupName) {
        return new static("There is no disk set for the backup named `{$backupName}`.");
    }
}
