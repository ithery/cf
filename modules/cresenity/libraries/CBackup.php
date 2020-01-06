<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup {

    /**
     * 
     * @return CBackup_BackupJob
     */
    public static function createJob() {
        $config = CF::config('backup');
        return CBackup_Factory::createJob($config);
    }

    /**
     * 
     * @return CBackup_Output
     */
    public static function output() {
        return CBackup_Output::instance();
    }

}
