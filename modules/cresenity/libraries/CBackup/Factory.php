<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Factory {

    public static function createJob($config) {
        return (new CBackup_BackupJob())
                        ->setFileSelection(static::createFileSelection($config['backup']['source']['files']))
                        ->setDatabaseDumpers(static::createDatabaseDumpers($config['backup']['source']['databases']))
                        ->setBackupDestinations(CBackup_BackupDestinationFactory::createFromArray($config['backup']));
    }

    protected static function createFileSelection(array $sourceFiles) {
        return CBackup_FileSelection::create($sourceFiles['include'])
                        ->excludeFilesFrom($sourceFiles['exclude'])
                        ->shouldFollowLinks(isset($sourceFiles['follow_links']) && $sourceFiles['follow_links']);
    }

    protected static function createDatabaseDumpers(array $dbConnectionNames) {
        return c::collect($dbConnectionNames)->mapWithKeys(function ( $dbConnectionName) {
                    return [$dbConnectionName => CBackup_DatabaseDumperFactory::createFromConnection($dbConnectionName)];
                });
    }

}
