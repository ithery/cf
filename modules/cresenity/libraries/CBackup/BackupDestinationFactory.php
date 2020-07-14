<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_BackupDestinationFactory {

    public static function createFromArray($disks) {
        return c::collect($disks)
                        ->map(function ($filesystemName) {
                            return CBackup_BackupDestination::create($filesystemName, CBackup::getConfig('backup.name'));
                        });
    }

}
