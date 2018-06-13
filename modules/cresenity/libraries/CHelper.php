<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:51:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CHelper {

    /**
     * 
     * @return \CHelper_File
     */
    public static function file() {
        return new CHelper_File();
    }

    public static function formatSize($bytes) {
        $si_prefix = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $base = 1024;
        $class = min((int) log($bytes, $base), count($si_prefix) - 1);
        if (pow($base, $class) == 0) {
            return 0;
        }
        return sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class];
    }

    public static function formatNumber($number) {
        return ctransform::thousand_separator($number);
    }

}
