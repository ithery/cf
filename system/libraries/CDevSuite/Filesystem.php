<?php

/**
 * Description of Filesystem.
 */
class CDevSuite_Filesystem {
    /**
     * Determine if the given path is a directory.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isDir($path) {
        return is_dir($path);
    }

    /**
     * Create a directory.
     *
     * @param string      $path
     * @param null|string $owner
     * @param int         $mode
     *
     * @return void
     */
    public function mkdir($path, $owner = null, $mode = 0755) {
        if (!mkdir($path, $mode, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        if ($owner) {
            $this->chown($path, $owner);
        }

        return true;
    }

    /**
     * Create a directory as root.
     *
     * @param string      $path
     * @param null|string $owner
     * @param int         $mode
     *
     * @return void
     */
    public function mkdirAsRoot($path, $owner = null, $mode = 0755) {
        CDevSuite::commandLine()->run(sprintf('sudo mkdir -m %o "%s"', $mode, $path));

        if ($owner) {
            CDevSuite::commandLine()->run(sprintf('sudo chown %s "%s"', $owner, $path));
        }
    }

    /**
     * Ensure that the given directory exists.
     *
     * @param string      $path
     * @param null|string $owner
     * @param int         $mode
     *
     * @return void
     */
    public function ensureDirExistsAsRoot($path, $owner = null, $mode = 0755) {
        if (!$this->isDir($path)) {
            return $this->mkdirAsRoot($path, $owner, $mode);
        }

        return false;
    }

    /**
     * Rename the given file or directory.
     *
     * @param string $oldname
     * @param string $newname
     *
     * @return void
     */
    public function rename($oldname, $newname) {
        rename($oldname, $newname);
    }

    /**
     * Rename the given file or directory as root.
     *
     * @param string $oldname
     * @param string $newname
     *
     * @return void
     */
    public function renameAsRoot($oldname, $newname) {
        $command = sprintf('sudo mv %s %s', $oldname, $newname);
        CDevSuite::commandLine()->run($command);
    }

    /**
     * Ensure that the given directory exists.
     *
     * @param string      $path
     * @param null|string $owner
     * @param int         $mode
     *
     * @return void
     */
    public function ensureDirExists($path, $owner = null, $mode = 0755) {
        if (!$this->isDir($path)) {
            return $this->mkdir($path, $owner, $mode);
        }

        return false;
    }

    /**
     * Create a directory as the non-root user.
     *
     * @param string $path
     * @param int    $mode
     *
     * @return void
     */
    public function mkdirAsUser($path, $mode = 0755) {
        return $this->mkdir($path, CDevSuite::user(), $mode);
    }

    /**
     * Touch the given path.
     *
     * @param string      $path
     * @param null|string $owner
     *
     * @return string
     */
    public function touch($path, $owner = null) {
        touch($path);

        if ($owner) {
            $this->chown($path, $owner);
        }

        return $path;
    }

    /**
     * Touch the given path as the non-root user.
     *
     * @param string $path
     *
     * @return void
     */
    public function touchAsUser($path) {
        return $this->touch($path, CDevSuite::user());
    }

    /**
     * Determine if the given file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists($path) {
        return file_exists($path);
    }

    /**
     * Read the contents of the given file.
     *
     * @param string $path
     *
     * @return string
     */
    public function get($path) {
        return file_get_contents($path);
    }

    /**
     * Write to the given file.
     *
     * @param string      $path
     * @param string      $contents
     * @param null|string $owner
     *
     * @return void
     */
    public function put($path, $contents, $owner = null) {
        file_put_contents($path, $contents);

        if ($owner) {
            $this->chown($path, $owner);
        }
    }

    /**
     * Write to the given file as the non-root user.
     *
     * @param string $path
     * @param string $contents
     *
     * @return void
     */
    public function putAsUser($path, $contents) {
        $this->put($path, $contents, CDevSuite::user());
    }

    /**
     * Write to the given file as root, via a temporary file copied into place.
     *
     * @param string $path
     * @param string $contents
     *
     * @return void
     */
    public function putAsRoot($path, $contents) {
        $tmp = tempnam(sys_get_temp_dir(), 'devsuite_');
        file_put_contents($tmp, $contents);
        // $localFile = CTemporary::createLocalFile($contents, 'devsuite');
        // $localFilename = $localFile->getFileName();

        try {
            $this->copyAsRoot($tmp, $path);
        } catch (Exception $e) {
            throw $e;
        } finally {
            @unlink($tmp);
        }
        // $this->copyAsRoot($localFilename, $path);

        // $localFile->delete();
    }

    /**
     * Append the contents to the given file.
     *
     * @param string      $path
     * @param string      $contents
     * @param null|string $owner
     *
     * @return void
     */
    public function append($path, $contents, $owner = null) {
        file_put_contents($path, $contents, FILE_APPEND);

        if ($owner) {
            $this->chown($path, $owner);
        }
    }

    /**
     * Append the contents to the given file as the non-root user.
     *
     * @param string $path
     * @param string $contents
     *
     * @return void
     */
    public function appendAsUser($path, $contents) {
        $this->append($path, $contents, CDevSuite::user());
    }

    /**
     * Copy the given file to a new location.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    public function copy($from, $to) {
        copy($from, $to);
    }

    /**
     * Copy the given file to a new location.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    public function copyAsRoot($from, $to) {
        $command = sprintf('sudo cp %s %s', $from, $to);
        CDevSuite::commandLine()->run($command);
    }

    /**
     * Copy the given file to a new location for the non-root user.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    public function copyAsUser($from, $to) {
        copy($from, $to);

        $this->chown($to, CDevSuite::user());
    }

    /**
     * Create a symlink to the given target.
     *
     * @param string $target
     * @param string $link
     *
     * @return void
     */
    public function symlink($target, $link) {
        if ($this->exists($link)) {
            $this->unlink($link);
        }

        symlink($target, $link);
    }

    /**
     * Create a symlink to the given target.
     *
     * @param string $target
     * @param string $link
     *
     * @return void
     */
    public function symlinkAsRoot($target, $link) {
        if ($this->exists($link)) {
            $this->unlinkAsRoot($link);
        }

        $command = sprintf('sudo ln -snf %s %s', $target, $link);
        CDevSuite::commandLine()->run($command);
    }

    /**
     * Create a symlink to the given target for the non-root user.
     *
     * This uses the command line as PHP can't change symlink permissions.
     *
     * @param string $target
     * @param string $link
     *
     * @return void
     */
    public function symlinkAsUser($target, $link) {
        if ($this->exists($link)) {
            $this->unlink($link);
        }

        CDevSuite::commandLine()->runAsUser('ln -s ' . escapeshellarg($target) . ' ' . escapeshellarg($link));
    }

    /**
     * Delete the file at the given path.
     *
     * @param string $path
     *
     * @return void
     */
    public function unlink($path) {
        if (file_exists($path) || is_link($path)) {
            @unlink($path);
        }
    }

    /**
     * Delete the file at the given path.
     *
     * @param string $path
     *
     * @return void
     */
    public function unlinkAsRoot($path) {
        if (file_exists($path) || is_link($path)) {
            $command = sprintf('sudo rm %s -f', $path);
            CDevSuite::commandLine()->run($command);
        }
    }

    /**
     * Change the owner of the given path.
     *
     * @param string $path
     * @param string $user
     *
     * @return void
     */
    public function chown($path, $user) {
        chown($path, $user);
    }

    /**
     * Change the group of the given path.
     *
     * @param string $path
     * @param string $group
     *
     * @return void
     */
    public function chgrp($path, $group) {
        chgrp($path, $group);
    }

    /**
     * Resolve the given path.
     *
     * @param string $path
     *
     * @return string
     */
    public function realpath($path) {
        return realpath($path);
    }

    /**
     * Determine if the given path is a symbolic link.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isLink($path) {
        return is_link($path);
    }

    /**
     * Resolve the given symbolic link.
     *
     * @param string $path
     *
     * @return string
     */
    public function readLink($path) {
        return readlink($path);
    }

    /**
     * Determine if the given path is a broken symbolic link.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isBrokenLink($path) {
        return is_link($path) && !file_exists($path);
    }

    /**
     * Remove all of the broken symbolic links at the given path.
     *
     * @param string $path
     *
     * @return void
     */
    public function removeBrokenLinksAt($path) {
        c::collect($this->scandir($path))->filter(function ($file) use ($path) {
            return $this->isBrokenLink($path . '/' . $file);
        })->each(function ($file) use ($path) {
            $this->unlink($path . '/' . $file);
        });
    }

    /**
     * Scan the given directory path.
     *
     * @param string $path
     *
     * @return array
     */
    public function scandir($path) {
        return c::collect(scandir($path))
            ->reject(function ($file) {
                return in_array($file, ['.', '..']);
            })->values()->all();
    }

    /**
     * Delete the given directory and its contents.
     *
     * @param string $directory
     *
     * @return bool
     */
    public function deleteDirectory($directory) {
        return CFile::deleteDirectory($directory);
    }

    /**
     * Delete the specified file or directory with files.
     *
     * @param string $files
     *
     * @return void
     */
    public function remove($files) {
        $files = iterator_to_array($this->toIterator($files));
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (!file_exists($file) && !is_link($file)) {
                continue;
            }

            if (is_dir($file) && !is_link($file)) {
                $this->remove(new \FilesystemIterator($file));

                if (true !== @rmdir($file)) {
                    throw new \Exception(sprintf('Failed to remove directory "%s".', $file));
                }
            } else {
                // https://bugs.php.net/bug.php?id=52176
                if ('\\' === DIRECTORY_SEPARATOR && is_dir($file)) {
                    if (true !== @rmdir($file)) {
                        throw new \Exception(sprintf('Failed to remove file "%s".', $file));
                    }
                } else {
                    if (true !== @unlink($file)) {
                        throw new \Exception(sprintf('Failed to remove file "%s".', $file));
                    }
                }
            }
        }
    }

    /**
     * Convert the given argument into a Traversable of file paths.
     *
     * @param mixed $files
     *
     * @return \Traversable
     */
    protected function toIterator($files) {
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject(is_array($files) ? $files : [$files]);
        }

        return $files;
    }
}
