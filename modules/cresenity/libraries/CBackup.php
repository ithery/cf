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

    public static function getMonitorCollection() {
        return CBackup_MonitorFactory::createForMonitorConfig();
    }

    public static function getMonitorStatusData() {
        $backupDestinationStatuses = static::getMonitorCollection();
        $rows = $backupDestinationStatuses->map(function (CBackup_Monitor $backupDestinationStatus) {
            return static::convertToRow($backupDestinationStatus);
        });
        return $rows;
    }

    public static function getMonitorFailureData() {
        $backupDestinationStatuses = static::getMonitorCollection();
        $failed = $backupDestinationStatuses
                ->filter(function (CBackup_Monitor $backupDestinationStatus) {
                    return $backupDestinationStatus->getHealthCheckFailure() !== null;
                })
                ->map(function (CBackup_Monitor $backupDestinationStatus) {
            return [
                $backupDestinationStatus->backupDestination()->backupName(),
                $backupDestinationStatus->backupDestination()->diskName(),
                $backupDestinationStatus->getHealthCheckFailure()->healthCheck()->name(),
                $backupDestinationStatus->getHealthCheckFailure()->exception()->getMessage(),
            ];
        });
        return $failed;
    }

    protected function convertToRow(CBackup_Monitor $backupDestinationStatus) {
        $destination = $backupDestinationStatus->backupDestination();
        $row = [
            'name' => $destination->backupName(),
            'disk' => $destination->diskName(),
            'isReachable' => CBackup_Helper::formatEmoji($destination->isReachable()),
            'isHealthy' => CBackup_Helper::formatEmoji($backupDestinationStatus->isHealthy()),
            'amount' => $destination->backups()->count(),
            'newest' => static::getFormattedBackupDate($destination->newestBackup()),
            'usedStorage' => CBackup_Helper::formatHumanReadableSize($destination->usedStorage()),
        ];
        if (!$destination->isReachable()) {
            foreach (['amount', 'newest', 'usedStorage'] as $propertyName) {
                $row[$propertyName] = '/';
            }
        }
        if ($backupDestinationStatus->getHealthCheckFailure() !== null) {
            $row['disk'] = '<error>' . $row['disk'] . '</error>';
        }
        return $row;
    }

    protected function getFormattedBackupDate(CBackup_Record $backup = null) {
        return is_null($backup) ? 'No backups present' : CBackup_Helper::formatAgeInDays($backup->date());
    }

}
