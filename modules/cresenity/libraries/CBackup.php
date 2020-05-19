<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup {

    public static function getConfig($key, $defaultValue = null) {
        return CBackup_Config::instance()->getConfig($key, $defaultValue);
    }

    /**
     * 
     * @return CBackup_BackupJob
     */
    public static function createJob($config = null) {
        CBackup_Config::instance()->reset();

        if ($config != null) {
            CBackup_Config::instance()->setConfig($config);
        }
        return CBackup_Factory::createJob();
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

    public static function houseKeeping($config = null) {
        CBackup_Config::instance()->reset();
        static::output()->clear();
        if ($config != null) {
            CBackup_Config::instance()->setConfig($config);
        }
        static::output()->info('Starting HouseKeeping...');

        $disableNotifications = true;



        try {
            $strategyClass = static::getConfig('house_keeping.strategy', CBackup_HouseKeeping_Strategy_DefaultStrategy::class);
            $strategy = new $strategyClass();
            static::output()->info('HouseKeeping run on strategy:'.$strategyClass);
            $backupDestinations = CBackup_BackupDestinationFactory::createFromArray(static::getConfig('backup.destination.disks'));
            
            $houseKeeping = new CBackup_HouseKeeping($backupDestinations, $strategy, $disableNotifications);

            $deletedFiles = $houseKeeping->run();

            static::output()->info('HouseKeeping completed!');
        } catch (Exception $exception) {
            CBackup::output()->error("HouseKeeping failed because {$exception->getMessage()}." . PHP_EOL . $exception->getTraceAsString());
            
            if (!$disableNotifications) {
                CEvent::dispatch(new CBackup_Event_HouseKeepingHasFailed($exception));
            }
            
            
        }
        $output = CBackup::output()->getAndClearOutput();
        return $output;
    }

}
