<?php

class CBackup_HouseKeeping {
    /**
     * @var CCollection
     */
    protected $backupDestinations;

    /**
     * @var CBackup_HouseKeeping_AbstractStrategy
     */
    protected $strategy;

    /**
     * @var bool
     */
    protected $sendNotifications = true;

    public function __construct(CCollection $backupDestinations, CBackup_HouseKeeping_AbstractStrategy $strategy, $disableNotifications = false) {
        $this->backupDestinations = $backupDestinations;

        $this->strategy = $strategy;

        $this->sendNotifications = !$disableNotifications;
    }

    public function run() {
        $this->backupDestinations->each(function (CBackup_BackupDestination $backupDestination) {
            try {
                if (!$backupDestination->isReachable()) {
                    throw new Exception("Could not connect to disk {$backupDestination->diskName()} because: {$backupDestination->connectionError()}");
                }

                CBackup::output()->info("Cleaning backups of {$backupDestination->backupName()} on disk {$backupDestination->diskName()}...");

                $this->strategy->deleteOldBackups($backupDestination->backups());
                $this->sendNotification(new CBackup_Event_HouseKeepingWasSuccessful($backupDestination));

                $usedStorage = CBackup_Helper::formatHumanReadableSize($backupDestination->fresh()->usedStorage());
                CBackup::output()->info("Used storage after cleanup: {$usedStorage}.");
            } catch (Exception $exception) {
                CBackup::output()->error("HouseKeeping failed because {$exception->getMessage()}." . PHP_EOL . $exception->getTraceAsString());

                $this->sendNotification(new CBackup_Event_HouseKeepingHasFailed($exception));

                throw $exception;
            }
        });
    }

    protected function sendNotification($notification) {
        if ($this->sendNotifications) {
            CEvent::dispatch($notification);
        }
    }
}
