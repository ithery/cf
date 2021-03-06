<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class CFile {
    /**
     * Check if a file exists in a path or url.
     *
     * @param string $file → path or file url
     *
     * @return bool
     */
    public static function exists($file) {
        if (filter_var($file, FILTER_VALIDATE_URL)) {
            $stream = stream_context_create(['http' => ['method' => 'HEAD']]);
            if ($content = @fopen($file, 'r', false, $stream)) {
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
     * @param string $path         filename to write the data to
     * @param string $contents     data to write to file
     * @param string $atomicSuffix lets you optionally provide a different
     *                             suffix for the temporary file
     *
     * @return mixed number of bytes written on success, otherwise FALSE
     */
    public static function putAtomic($path, $contents, $atomicSuffix = 'atomictmp') {
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
     * @param string $path
     * @param string $contents
     * @param bool   $lock
     *
     * @return int|bool
     */
    public static function put($path, $contents, $lock = false) {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @param bool   $lock
     *
     * @throws CFile_Exception_FileNotFoundException
     *
     * @return string
     */
    public static function get($path, $lock = false) {
        if (static::isFile($path)) {
            return $lock ? static::sharedGet($path) : file_get_contents($path);
        }

        throw new CStorage_Exception_FileNotFoundException("File does not exist at path {$path}");
    }

    /**
     * Delete the file at a given path.
     *
     * @param string|array $paths
     *
     * @return bool
     */
    public static function delete($paths) {
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
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     * @param bool   $force
     *
     * @return bool
     */
    public static function makeDirectory($path, $mode = 0755, $recursive = false, $force = false) {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }

        return mkdir($path, $mode, $recursive);
    }

    /**
     * Get the file's last modification time.
     *
     * @param string $path
     *
     * @return int
     */
    public static function lastModified($path) {
        return filemtime($path);
    }

    /**
     * Give the diff of time modified file and given time parameters
     * Use current time if parameter $time not passed.
     *
     * @param string     $filename
     * @param string|int $time
     *
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
     * @param string $file
     *
     * @return bool
     */
    public static function isFile($file) {
        return is_file($file);
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param string $path
     *
     * @return string
     */
    public static function sharedGet($path) {
        $contents = '';
        $handle = fopen($path, 'rb');
        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);
                    $contents = fread($handle, static::size($path) ?: 1);
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
     * @param string $path
     *
     * @return int
     */
    public static function size($path) {
        clearstatcache();
        $filesize = filesize($path);
        if ($filesize == 0 && static::exists($path)) {
            //try to get another method
            $fp = fopen($path, 'rb');
            fseek($fp, 0, SEEK_END);
            $filesize = ftell($fp);
            fclose($fp);
        }

        return $filesize;
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param string $directory
     *
     * @return bool
     */
    public static function isDirectory($directory) {
        return is_dir($directory);
    }

    /**
     * Determine if the given path is readable.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isReadable($path) {
        return is_readable($path);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isWritable($path) {
        return is_writable($path);
    }

    /**
     * Get the file type of a given file.
     *
     * @param string $path
     *
     * @return string
     */
    public static function type($path) {
        return filetype($path);
    }

    /**
     * Extract the file name from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function name($path) {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Extract the trailing name component from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function basename($path) {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Extract the parent directory from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function dirname($path) {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function extension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param string $path
     *
     * @return string|false
     */
    public static function mimeType($path) {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param string $directory
     *
     * @return array
     */
    public static function directories($directory) {
        $directories = [];
        foreach (Finder::create()->in($directory)->directories()->depth(0)->sortByName() as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }

    /**
     * Recursively delete a directory.
     *
     * The directory itself may be optionally preserved.
     *
     * @param string $directory
     * @param bool   $preserve
     *
     * @return bool
     */
    public static function deleteDirectory($directory, $preserve = false) {
        if (!static::isDirectory($directory)) {
            return false;
        }
        $items = new FilesystemIterator($directory);
        foreach ($items as $item) {
            // If the item is a directory, we can just recurse into the function and
            // delete that sub-directory otherwise we'll just delete the file and
            // keep iterating through each file until the directory is cleaned.
            if ($item->isDir() && !$item->isLink()) {
                static::deleteDirectory($item->getPathname());
            } else {
                // If the item is just a file, we can go ahead and delete it since we're
                // just looping through and waxing all of the files in this directory
                // and calling directories recursively, so we delete the real path.
                static::delete($item->getPathname());
            }
        }
        if (!$preserve) {
            @rmdir($directory);
        }

        return true;
    }

    /**
     * Remove all of the directories within a given directory.
     *
     * @param string $directory
     *
     * @return bool
     */
    public static function deleteDirectories($directory) {
        $allDirectories = static::directories($directory);
        if (!empty($allDirectories)) {
            foreach ($allDirectories as $directoryName) {
                static::deleteDirectory($directoryName);
            }

            return true;
        }

        return false;
    }

    /**
     * Empty the specified directory of all files and folders.
     *
     * @param string $directory
     *
     * @return bool
     */
    public static function cleanDirectory($directory) {
        return static::deleteDirectory($directory, true);
    }

    /**
     * Get the returned value of a file.
     *
     * @param string $path
     * @param array  $data
     *
     * @throws CStorage_Exception_FileNotFoundException
     *
     * @return mixed
     */
    public static function getRequire($path, array $data = []) {
        if (static::isFile($path)) {
            $__path = $path;
            $__data = $data;
            $function = static function () use ($__path, $__data) {
                extract($__data, EXTR_SKIP);

                return require $__path;
            };

            return $function();
        }

        throw new CStorage_Exception_FileNotFoundException("File does not exist at path {$path}");
    }

    public static function phpValue($val, $level = 0) {
        $indentString = '    ';
        $str = '';
        $eol = PHP_EOL;
        $indent = str_repeat($indentString, $level);
        if (is_array($val)) {
            $str .= '[' . $eol;
            $indent2 = $indent . $indentString;
            foreach ($val as $k => $v) {
                $str .= $indent2 . "'" . addslashes($k) . "'=>";
                $str .= static::phpValue($v, $level + 1);
                $str .= ',' . $eol;
            }
            $str .= $indent . ']';
        } elseif (is_null($val)) {
            $str .= 'NULL';
        } elseif (is_bool($val)) {
            $str .= ($val === true ? 'TRUE' : 'FALSE');
        } else {
            $str .= "'" . addslashes($val) . "'";
        }

        return $str;
    }

    public static function putPhpValue($filename, $data, $lock = true) {
        $val = '<?php ' . PHP_EOL . 'return ' . static::phpValue($data) . ';';

        return static::put($filename, $val, $lock);
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param string $directory
     * @param bool   $hidden
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public static function allFiles($directory, $hidden = false) {
        return iterator_to_array(
            Finder::create()->files()->ignoreDotFiles(!$hidden)->in($directory)->sortByName(),
            false
        );
    }

    /**
     * Move a file to a new location.
     *
     * @param string $path
     * @param string $target
     *
     * @return bool
     */
    public static function move($path, $target) {
        return rename($path, $target);
    }

    /**
     * Copy a file to a new location.
     *
     * @param string $path
     * @param string $target
     *
     * @return bool
     */
    public static function copy($path, $target) {
        return copy($path, $target);
    }

    /**
     * Create a symlink to the target file or directory. On Windows, a hard link is created if the target is a file.
     *
     * @param string $target
     * @param string $link
     *
     * @return void
     */
    public static function link($target, $link) {
        if (!CServer::isWindows()) {
            return symlink($target, $link);
        }

        $mode = static::isDirectory($target) ? 'J' : 'H';

        exec("mklink /{$mode} " . escapeshellarg($link) . ' ' . escapeshellarg($target));
    }

    /**
     * Create a relative symlink to the target file or directory.
     *
     * @param string $target
     * @param string $link
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public static function relativeLink($target, $link) {
        if (!class_exists(SymfonyFilesystem::class)) {
            throw new RuntimeException(
                'To enable support for relative links, please install the symfony/filesystem package.'
            );
        }

        $relativeTarget = (new SymfonyFilesystem())->makePathRelative($target, dirname($link));

        static::link($relativeTarget, $link);
    }

    /**
     * Get or set UNIX mode of a file or directory.
     *
     * @param string   $path
     * @param null|int $mode
     *
     * @return mixed
     */
    public static function chmod($path, $mode = null) {
        if ($mode) {
            return chmod($path, $mode);
        }

        return substr(sprintf('%o', fileperms($path)), -4);
    }

    /**
     * Find path names matching a given pattern.
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return array
     */
    public static function glob($pattern, $flags = 0) {
        return glob($pattern, $flags);
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param string $directory
     * @param bool   $hidden
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public static function files($directory, $hidden = false) {
        return iterator_to_array(
            Finder::create()->files()->ignoreDotFiles(!$hidden)->in($directory)->depth(0)->sortByName(),
            false
        );
    }
}
