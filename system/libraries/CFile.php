<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 10:18:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CFile {

    /**
     * Check if a file exists in a path or url.
     *
     * @param string $file → path or file url
     * @return bool
     */
    public static function exists($file) {
        if (filter_var($file, FILTER_VALIDATE_URL)) {
            $stream = stream_context_create(['http' => ['method' => 'HEAD']]);
            if ($content = @fopen($file, 'r', null, $stream)) {
                $headers = stream_get_meta_data($content);
                fclose($content);
                $status = substr($headers['wrapper_data'][0], 9, 3);
                return $status >= 200 && $status < 400;
            }
            return false;
        }
        return file_exists($file) && is_file($file);
    }

    /**
     * Atomic filewriter.
     *
     * Safely writes new contents to a file using an atomic two-step process.
     * If the script is killed before the write is complete, only the temporary
     * trash file will be corrupted.
     *
     * @param string $filename     Filename to write the data to.
     * @param string $data         Data to write to file.
     * @param string $atomicSuffix Lets you optionally provide a different
     *                             suffix for the temporary file.
     *
     * @return mixed Number of bytes written on success, otherwise FALSE.
     */
    public static function setContent($filename, $data, $atomicSuffix = 'atomictmp') {
        // Perform an exclusive (locked) overwrite to a temporary file.
        $filenameTmp = sprintf('%s.%s', $filename, $atomicSuffix);
        $writeResult = @file_put_contents($filenameTmp, $data, LOCK_EX);
        if ($writeResult !== false) {
            // Now move the file to its real destination (replaced if exists).
            $moveResult = @rename($filenameTmp, $filename);
            if ($moveResult === true) {
                // Successful write and move. Return number of bytes written.
                return $writeResult;
            }
        }

        return false; // Failed.
    }

    public static function getContent($file) {
        if (self::exists($file)) {
            return @file_get_contents($file);
        }
        return false;
    }

    /**
     * Create directory.
     *
     * @param string $path → path where to create directory
     *
     * @return bool
     */
    public static function createDir($path) {
        return !is_dir($path) && @mkdir($path, 0777, true);
    }

    /**
     * 
     * @param string $filename
     * @return type
     */
    public static function mtime($filename) {
        return filemtime($filename);
    }

    /**
     * Give the diff of time modified file and given time parameters
     * Use current time if parameter $time not passed
     * 
     * @param string $file
     * @param string|int $time 
     * @return int diff in second
     */
    public static function mtimeDiff($filename, $time = null) {
        if ($time == null) {
            $time = time();
        }
        if (is_string($time)) {
            $time = strtotime($time);
        }
        return $time - self::mtime($filename);
    }

}
