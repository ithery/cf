<?php
class CHouseKeeping_FileTemp_BackupFileTemp {
    public static function execute($keepDays = 90) {
        $executed = false;

        $disk = CTemporary::disk();

        $basePath = 'backup';

        $directories = $disk->directories($basePath);
        foreach ($directories as $directory) {
            //get last path
            $folder = carr::last(explode('/', $directory));
            $ymd = cstr::substr($folder, 0, 8);
            if (strlen($ymd) == 8) {
                //the format maybe is ymd
                //try to parse it to carbon
                $carbonDate = CCarbon::parse($ymd);

                $days = $carbonDate->diffInDays(CCarbon::now());

                if ($days > $keepDays) {
                    if (CDaemon::isDaemon()) {
                        CDaemon::log('deleting folder ' . $directory);
                    }

                    $disk->deleteDirectory($directory);
                    $executed = true;

                    break;
                }
            }
        }

        return $executed;
    }
}
