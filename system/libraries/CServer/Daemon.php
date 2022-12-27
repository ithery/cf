<?php

class CServer_Daemon {
    public static function getAllDaemons() {
        return c::collect(CServer_Process::psList('cresenity/daemon'))->map(function ($item) {
            $command = carr::get($item, 'command');
            if (preg_match('#serviceClass=(.+?)&#', $command, $matches)) {
                return $matches[1];
            }

            return null;
        })->filter()->toArray();
    }

    public static function getDuplicateDaemons() {
        $arr = static::getAllDaemons();
        // Convert every value to uppercase, and remove duplicate values
        $withoutDuplicates = array_unique($arr);

        // The difference in the original array, and the $withoutDuplicates array
        // will be the duplicate values
        $duplicates = array_diff($arr, $withoutDuplicates);

        return $duplicates;
    }
}
