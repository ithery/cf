<?php

class CBackup_Monitor_HealthCheck_MaximumStorageInMegabytes extends CBackup_Monitor_AbstractHealthCheck {
    /**
     * @var int
     */
    protected $maximumSizeInMegaBytes;

    public function __construct($maximumSizeInMegaBytes = 5000) {
        $this->maximumSizeInMegaBytes = $maximumSizeInMegaBytes;
    }

    public function checkHealth(CBackup_BackupDestination $backupDestination) {
        $usageInBytes = $backupDestination->usedStorage();
        $this->failIf(
            $this->exceedsAllowance($usageInBytes),
            clang::__('backup.unhealthy_backup_found_full', [
                ':disk_usage' => $this->humanReadableSize($usageInBytes),
                ':disk_limit' => $this->humanReadableSize($this->bytes($this->maximumSizeInMegaBytes)),
            ])
        );
    }

    protected function exceedsAllowance($usageInBytes) {
        return $usageInBytes > $this->bytes($this->maximumSizeInMegaBytes);
    }

    protected function bytes($megaBytes) {
        return $megaBytes * 1024 * 1024;
    }

    protected function humanReadableSize($sizeInBytes) {
        return CBackup_Helper::formatHumanReadableSize($sizeInBytes);
    }
}
