<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 10:02:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTemporary {

    /**
     * 
     * @param string $path
     * @return \CTemporary_Directory
     */
    public static function createDirectory($path) {
        return new CTemporary_Directory($path);
    }

    /**
     * 
     * @param string $filename
     * @return \CTemporary_File
     */
    public static function createFile($filename) {
        return new CTemporary_File(self::createDirectory(dirname($filename)), basename($filename));
    }

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
                if (strlen($c) == 0) {
                    $c = "_";
                }
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

    
    /**
     * 
     * @param string $folder
     * @param string $filename
     * @return string
     */
    public static function delete($folder, $filename) {
        

        $path= static::makePath($folder, $filename);
        return @unlink($path);
        
    }
}
