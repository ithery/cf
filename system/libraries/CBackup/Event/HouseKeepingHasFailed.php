<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Event_HouseKeepingHasFailed {

    /** @var \Exception */
    public $exception;

    /** @var CBackup_BackupDestination|null */
    public $backupDestination;

    public function __construct(Exception $exception, CBackup_BackupDestination $backupDestination = null) {
        $this->exception = $exception;

        $this->backupDestination = $backupDestination;
    }

}
