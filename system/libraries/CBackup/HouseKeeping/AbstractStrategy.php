<?php

abstract class CBackup_HouseKeeping_AbstractStrategy {
    public function __construct() {
    }

    abstract public function deleteOldBackups(CBackup_RecordCollection $backups);
}
