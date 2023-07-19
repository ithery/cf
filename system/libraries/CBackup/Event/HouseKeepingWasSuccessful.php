<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Event_HouseKeepingWasSuccessful {

    /** @var CBackup_BackupDestination */
    public $backupDestination;

    public function __construct(CBackup_BackupDestination $backupDestination) {
        $this->backupDestination = $backupDestination;
    }

}
