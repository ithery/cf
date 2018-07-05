<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 10:14:58 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Temp {

    /**
     * 
     * @return string
     */
    public static function getDirectory() {
        $path = DOCROOT . "temp" . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    public static function makeDir($path) {
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    /**
     * 
     * @param string $path
     * @param string $folder
     * @return string
     */
    public static function makeFolder($path, $folder) {
        $path = $path . $folder . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    /**
     * 
     * @param string $folder
     * @param string $filename
     * @return string
     */
    public static function makePath($folder, $filename) {
        $depth = 5;
        $mainFolder = substr($filename, 0, 8);
        $path = self::getDirectory();
        $path = self::makeFolder($path, $folder);
        $path = self::makeFolder($path, $mainFolder);
        $basefile = basename($filename);
        for ($i = 0; $i < $depth; $i++) {
            $c = "_";
            if (strlen($basefile) > ($i + 1)) {
                $c = substr($basefile, $i + 8, 1);
                if (strlen($c) == 0)
                    $c = "_";
                $path = self::makefolder($path, $c);
            }
        }

        return $path . $filename;
    }

    /**
     * 
     * @param string $folder
     * @param string $filename
     * @return string
     */
    public static function getUrl($folder, $filename) {
        $mainFolder = substr($filename, 0, 8);
        $basefile = basename($filename);
        $url = curl::base() . 'temp/' . $folder . '/' . $mainFolder . '/';
        $depth = 5;
        for ($i = 0; $i < $depth; $i++) {
            $c = "_";
            if (strlen($basefile) > ($i + 1)) {
                $c = substr($basefile, $i + 8, 1);
                if (strlen($c) == 0) {
                    $c = "_";
                }
                $url .= $c . '/';
            }
        }
        return $url . $filename;
    }

}
