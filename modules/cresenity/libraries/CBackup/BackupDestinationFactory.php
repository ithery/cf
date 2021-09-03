<?php

class CBackup_BackupDestinationFactory {
    public static function createFromArray($disks) {
        return c::collect($disks)
            ->map(function ($filesystemName) {
                return CBackup_BackupDestination::create($filesystemName, CBackup::getConfig('backup.name'));
            });
    }
}
