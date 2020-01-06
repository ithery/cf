<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Exception_InvalidBackupJobException extends Exception {

    public static function noDestinationsSpecified() {
        return new static('A backup job cannot run without a destination to backup to!');
    }

    public static function destinationDoesNotExist($diskName) {
        return new static("There is no backup destination with a disk named `{$diskName}`.");
    }

    public static function noFilesToBeBackedUp() {
        return new static('There are no files to be backed up.');
    }

}
