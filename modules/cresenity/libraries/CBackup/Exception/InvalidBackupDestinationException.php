<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Exception_InvalidBackupDestinationException extends Exception {

    public static function diskNotSet($backupName) {
        return new static("There is no disk set for the backup named `{$backupName}`.");
    }

}
