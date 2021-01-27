<?php

/**
 * Helper ctemp
 *
 * @deprecated 1.2 use CTemporary
 */
// @codingStandardsIgnoreStart
class ctemp {
    public static function get_directory() {
        $path = DOCROOT . 'temp' . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    public static function makedir($path) {
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    public static function makefolder($path, $folder) {
        $path = $path . $folder . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    public static function makepath($folder, $filename) {
        $depth = 5;
        $main_folder = substr($filename, 0, 8);
        $path = ctemp::get_directory();
        $path = ctemp::makefolder($path, $folder);
        $path = ctemp::makefolder($path, $main_folder);
        $basefile = basename($filename);
        for ($i = 0; $i < $depth; $i++) {
            $c = '_';
            if (strlen($basefile) > ($i + 1)) {
                $c = substr($basefile, $i + 8, 1);
                if (strlen($c) == 0) {
                    $c = '_';
                }
                $path = ctemp::makefolder($path, $c);
            }
        }

        return $path . $filename;
    }

    public static function get_url($folder, $filename) {
        return CTemporary::getUrl($folder, $filename);
    }
}
// @codingStandardsIgnoreEnd
