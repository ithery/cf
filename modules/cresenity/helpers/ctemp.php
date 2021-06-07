<?php

/**
 * Helper ctemp
 *
 * @deprecated 1.2 use CTemporary
 */
// @codingStandardsIgnoreStart
class ctemp {
    public static function get_directory() {
        return CTemporary::getDirectory();
    }

    public static function makedir($path) {
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    public static function makefolder($path, $folder) {
        return CTemporary::makeFolder($path, $folder);
    }

    public static function makepath($folder, $filename) {
        return CTemporary::makePath($folder, $filename);
    }

    public static function get_url($folder, $filename) {
        return CTemporary::getUrl($folder, $filename);
    }
}
// @codingStandardsIgnoreEnd
