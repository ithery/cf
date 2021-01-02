<?php

class CBackup_Monitor_HealthCheck_IsReachable extends CBackup_Monitor_AbstractHealthCheck {
    public function checkHealth(CBackup_BackupDestination $backupDestination) {
        $this->failUnless(
            $backupDestination->isReachable(),
            clang::__('backup.unhealthy_backup_found_not_reachable', [
                ':error' => $backupDestination->connectionError,
            ])
        );
    }
}
