<?php
class CHouseKeeping_FileTemp_AjaxFileTemp {
    public static function execute($keepDays = 90) {
        $executed = false;

        $disk = CTemporary::disk();

        $basePath = 'ajax';

        $directories = $disk->directories($basePath);
        foreach ($directories as $directory) {
            //get last path
            $ymd = carr::last(explode('/', $directory));
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
