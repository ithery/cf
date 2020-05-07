<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CBackup_HouseKeeping_AbstractStrategy {

    public function __construct() {
        
    }

    abstract public function deleteOldBackups(CBackup_RecordCollection $backups);
}
