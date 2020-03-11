<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Monitor_HealthCheck_MaximumAgeInDays extends CBackup_Monitor_AbstractHealthCheck {

    /** @var int */
    protected $days;

    public function __construct($days = 1) {
        $this->days = $days;
    }

    public function checkHealth(CBackup_BackupDestination $backupDestination) {
        $this->failIf(
                $this->hasNoBackups($backupDestination), clang::__('backup:.unhealthy_backup_found_empty')
        );
        $newestBackup = $backupDestination->backups()->newest();
        $this->failIf(
                $this->isTooOld($newestBackup), clang::__('backup.unhealthy_backup_found_old', [':date' => $newestBackup->date()->format('Y/m/d h:i:s')])
        );
    }

    protected function hasNoBackups(CBackup_BackupDestination $backupDestination) {
        return $backupDestination->backups()->isEmpty();
    }

    protected function isTooOld(CBackup_Record $backup) {
        if (is_null($this->days)) {
            return false;
        }
        if ($backup->date()->gt(c::now()->subDays($this->days))) {
            return false;
        }
        return true;
    }

}
