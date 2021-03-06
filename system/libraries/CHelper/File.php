<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 5:11:09 AM
 */
use Symfony\Component\Finder\Finder;

class CHelper_File {
    /**
     * Determine if a file or directory exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function exists($path) {
        return file_exists($path);
    }

    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @param bool   $lock
     *
     * @throws CStorage_Exception_FileNotFoundException
     *
     * @return string
     */
    public static function get($path, $lock = false) {
        if (self::isFile($path)) {
            return $lock ? self::sharedGet($path) : file_get_contents($path);
        }

        throw new CStorage_Exception_FileNotFoundException('File does not exist at path :path', [':path' => $path]);
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

                    $contents = fread($handle, self::size($path) ?: 1);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * Get the returned value of a file.
     *
     * @param string $path
     *
     * @throws CStorage_Exception_FileNotFoundException
     *
     * @return mixed
     */
    public static function getRequire($path) {
        if (self::isFile($path)) {
            return require $path;
        }

        throw new CStorage_Exception_FileNotFoundException('File does not exist at path :path', [':path' => $path]);
    }

    /**
     * Require the given file once.
     *
     * @param string $file
     *
     * @return mixed
     */
    public static function requireOnce($file) {
        require_once $file;
    }

    /**
     * Get the MD5 hash of the file at the given path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function hash($path) {
        return md5_file($path);
    }

    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param string $contents
     * @param bool   $lock
     *
     * @return int
     */
    public static function put($path, $contents, $lock = false) {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Prepend to a file.
     *
     * @param string $path
     * @param string $data
     *
     * @return int
     */
    public static function prepend($path, $data) {
        if (self::exists($path)) {
            return self::put($path, $data . self::get($path));
        }

        return self::put($path, $data);
    }

    /**
     * Append to a file.
     *
     * @param string $path
     * @param string $data
     *
     * @return int
     */
    public static function append($path, $data) {
        return file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * Get or set UNIX mode of a file or directory.
     *
     * @param string $path
     * @param int    $mode
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
     * Create a hard link to the target file or directory.
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

        $mode = self::isDirectory($target) ? 'J' : 'H';

        exec("mklink /{$mode} \"{$link}\" \"{$target}\"");
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
     * Get the file size of a given file.
     *
     * @param string $path
     *
     * @return int
     */
    public static function size($path) {
        return filesize($path);
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
            Finder::create()->files()->ignoreDotFiles(!$hidden)->in($directory)->depth(0),
            false
        );
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
            Finder::create()->files()->ignoreDotFiles(!$hidden)->in($directory),
            false
        );
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

        foreach (Finder::create()->in($directory)->directories()->depth(0) as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
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

        return @mkdir($path, $mode, $recursive);
    }

    /**
     * Move a directory.
     *
     * @param string $from
     * @param string $to
     * @param bool   $overwrite
     *
     * @return bool
     */
    public static function moveDirectory($from, $to, $overwrite = false) {
        if ($overwrite && self::isDirectory($to)) {
            if (!self::deleteDirectory($to)) {
                return false;
            }
        }

        return @rename($from, $to) === true;
    }

    /**
     * Copy a directory from one location to another.
     *
     * @param string $directory
     * @param string $destination
     * @param int    $options
     *
     * @return bool
     */
    public static function copyDirectory($directory, $destination, $options = null) {
        if (!self::isDirectory($directory)) {
            return false;
        }

        $options = $options ?: FilesystemIterator::SKIP_DOTS;

        // If the destination directory does not actually exist, we will go ahead and
        // create it recursively, which just gets the destination prepared to copy
        // the files over. Once we make the directory we'll proceed the copying.
        if (!self::isDirectory($destination)) {
            self::makeDirectory($destination, 0777, true);
        }

        $items = new FilesystemIterator($directory, $options);

        foreach ($items as $item) {
            // As we spin through items, we will check to see if the current file is actually
            // a directory or a file. When it is actually a directory we will need to call
            // back into this function recursively to keep copying these nested folders.
            $target = $destination . '/' . $item->getBasename();

            if ($item->isDir()) {
                $path = $item->getPathname();

                if (!self::copyDirectory($path, $target, $options)) {
                    return false;
                }
            } else {
                // If the current items is just a regular file, we will just copy this to the new
                // location and keep looping. If for some reason the copy fails we'll bail out
                // and return false, so the developer is aware that the copy process failed.
                if (!self::copy($item->getPathname(), $target)) {
                    return false;
                }
            }
        }

        return true;
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
        if (!self::isDirectory($directory)) {
            return false;
        }

        $items = new FilesystemIterator($directory);

        foreach ($items as $item) {
            // If the item is a directory, we can just recurse into the function and
            // delete that sub-directory otherwise we'll just delete the file and
            // keep iterating through each file until the directory is cleaned.
            if ($item->isDir() && !$item->isLink()) {
                self::deleteDirectory($item->getPathname());
            } else {
                // If the item is just a file, we can go ahead and delete it since we're
                // just looping through and waxing all of the files in this directory
                // and calling directories recursively, so we delete the real path.
                self::delete($item->getPathname());
            }
        }

        if (!$preserve) {
            @rmdir($directory);
        }

        return true;
    }

    /**
     * Empty the specified directory of all files and folders.
     *
     * @param string $directory
     *
     * @return bool
     */
    public static function cleanDirectory($directory) {
        return self::deleteDirectory($directory, true);
    }
}
