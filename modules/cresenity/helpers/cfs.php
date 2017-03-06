<?php

defined('SYSPATH') OR die('No direct access allowed.');

class cfs {

    public static function list_files_in_dir($dir, &$results = array(), $ignore_dir = array()) {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != ".." && !in_array($value, $ignore_dir)) {
                self::list_files_in_dir($path, $results, $ignore_dir);
                $results[] = $path;
            }
        }

        return $results;
    }

    public static function list_files($dir) {
        $result = array();
        $dir = rtrim($dir, DS) . DS;
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file == "." || $file == ".." || $file == "Thumbs.db")
                        continue;
                    if (is_dir($dir . $file))
                        continue;
                    $result[] = $dir . $file;
                }
                closedir($handle);
            }
        }
        return $result;
    }

    public static function list_dir($dir) {
        $result = array();
        $dir = trim($dir, DS) . DS;
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file == "." || $file == ".." || $file == "Thumbs.db")
                        continue;
                    if (!is_dir($dir . $file))
                        continue;
                    $result[] = $dir . $file;
                }
                closedir($handle);
            }
        }
        return $result;
    }

    public static function delete_dir($dir, $virtual = false) {

        $ds = DIRECTORY_SEPARATOR;
        $dir = $virtual ? realpath($dir) : $dir;
        $dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;
        if (is_dir($dir) && $handle = opendir($dir)) {
            while ($file = readdir($handle)) {
                if ($file == '.' || $file == '..') {
                    continue;
                } elseif (is_dir($dir . $ds . $file)) {
                    self::delete_dir($dir . $ds . $file);
                } else {
                    unlink($dir . $ds . $file);
                }
            }
            closedir($handle);
            rmdir($dir);
            return true;
        } else {
            return false;
        }
    }

    public static function basename($str) {
        return basename($str);
    }

    public static function mkdir($dir) {
        return mkdir($dir);
    }

    public static function is_dir($dir) {
        return is_dir($dir);
    }

    public static function is_file($value) {
        $value = strval(str_replace("\0", "", $value));

        return is_file($value);
    }

    public static function file_exists($value) {
        if (!cfs::is_file($value))
            return false;
        return file_exists($value);
    }

    public static function mtime($file) {
        return filemtime($file);
    }

    public static function mtime_diff($file, $time = null) {
        if ($time == null) {
            return time() - cfs::mtime($file);
        }
        if (is_string($time))
            $time = strtotime($time);
        return $time - cfs::mtime($file);
    }

}
