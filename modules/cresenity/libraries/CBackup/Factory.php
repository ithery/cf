<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Factory {

    public static function createJob() {
        return (new CBackup_BackupJob())
                        ->setFileSelection(static::createFileSelection(CBackup::getConfig('backup.source.files')))
                        ->setDatabaseDumpers(static::createDatabaseDumpers(CBackup::getConfig('backup.source.databases')))
                        ->setBackupDestinations(CBackup_BackupDestinationFactory::createFromArray(CBackup::getConfig('backup.destination.disks')));
    }

    protected static function createFileSelection(array $sourceFiles) {
        return CBackup_FileSelection::create($sourceFiles['include'])
                        ->excludeFilesFrom($sourceFiles['exclude'])
                        ->shouldFollowLinks(isset($sourceFiles['follow_links']) && $sourceFiles['follow_links']);
    }

    protected static function createDatabaseDumpers(array $dbConnectionNames) {
        return c::collect($dbConnectionNames)->mapWithKeys(function ( $dbConnectionName) {
                    $name = $dbConnectionName;
                    if(is_array($name)) {
                        $name = carr::get($dbConnectionName,'connection.database').'-'.uniqid();
                    }
                    return [$name => CBackup_DatabaseDumperFactory::createFromConnection($dbConnectionName)];
                });
    }

}
