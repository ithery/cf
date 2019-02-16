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
    public function exists($file) {
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
    public function putAtomic($path, $contents, $atomicSuffix = 'atomictmp') {
        // Perform an exclusive (locked) overwrite to a temporary file.
        $pathTemp = sprintf('%s.%s', $path, $atomicSuffix);
        $writeResult = @file_put_contents($pathTemp, $contents, LOCK_EX);
        if ($writeResult !== false) {
            // Now move the file to its real destination (replaced if exists).
            $moveResult = @rename($pathTemp, $path);
            if ($moveResult === true) {
                // Successful write and move. Return number of bytes written.
                return $writeResult;
            }
        }

        return false; // Failed.
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int|bool
     */
    public function put($path, $contents, $lock = false) {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     *
     * @throws CFile_Exception_FileNotFoundException
     */
    public function get($path, $lock = false) {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }
        throw new CFile_Exception_FileNotFoundException("File does not exist at path {$path}");
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

    /**
     * Determine if the given path is a file.
     *
     * @param  string  $file
     * @return bool
     */
    public function isFile($file) {
        return is_file($file);
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param  string  $path
     * @return string
     */
    public function sharedGet($path) {
        $contents = '';
        $handle = fopen($path, 'rb');
        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);
                    $contents = fread($handle, $this->size($path) ?: 1);
                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }
        return $contents;
    }

}
