<?php

class CHouseKeeping_FileTemp {
    public static function cleanAjaxTemp($keepDays = 90) {
        return CHouseKeeping_FileTemp_AjaxFileTemp::execute($keepDays);
    }

    public static function cleanBackupTemp($keepDays = 90) {
        return CHouseKeeping_FileTemp_BackupFileTemp::execute($keepDays);
    }
}
