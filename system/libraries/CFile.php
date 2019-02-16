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
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     */
    public function delete($paths) {
        $paths = is_array($paths) ? $paths : func_get_args();
        $success = true;
        foreach ($paths as $path) {
            try {
                if (!@unlink($path)) {
                    $success = false;
                }
            } catch (ErrorException $e) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  int     $mode
     * @param  bool    $recursive
     * @param  bool    $force
     * @return bool
     */
    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false) {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path) {
        return filemtime($path);
    }

    /**
     * Give the diff of time modified file and given time parameters
     * Use current time if parameter $time not passed
     * 
     * @param string $file
     * @param string|int $time 
     * @return int diff in second
     */
    public static function lastModifiedDiff($filename, $time = null) {
        if ($time == null) {
            $time = time();
        }
        if (is_string($time)) {
            $time = strtotime($time);
        }
        return $time - self::lastModified($filename);
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

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size($path) {
        return filesize($path);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function isDirectory($directory) {
        return is_dir($directory);
    }

    /**
     * Determine if the given path is readable.
     *
     * @param  string  $path
     * @return bool
     */
    public function isReadable($path) {
        return is_readable($path);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param  string  $path
     * @return bool
     */
    public function isWritable($path) {
        return is_writable($path);
    }

    /**
     * Get the file type of a given file.
     *
     * @param  string  $path
     * @return string
     */
    public function type($path) {
        return filetype($path);
    }

    /**
     * Extract the file name from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function name($path) {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Extract the trailing name component from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function basename($path) {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Extract the parent directory from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function dirname($path) {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function extension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param  string  $path
     * @return string|false
     */
    public function mimeType($path) {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

}
