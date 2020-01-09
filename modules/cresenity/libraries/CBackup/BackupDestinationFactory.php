<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_BackupDestinationFactory {

    public static function createFromArray($config) {
        return c::collect($config['destination']['disks'])
                        ->map(function ($filesystemName) use ($config) {
                            return CBackup_BackupDestination::create($filesystemName, $config['name']);
                        });
    }

}
