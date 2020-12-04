<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Linux_Filesystem extends CDevSuite_Filesystem {

    private function toIterator($files) {
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject(is_array($files) ? $files : [$files]);
        }

        return $files;
    }

    /**
     * Delete the specified file or directory with files.
     *
     * @param string $files
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
                        throw new \Exception(sprintf('Failed to remove file "%s".', $file), 0, null, $file);
                    }
                }
            }
        }
    }

    protected function removeDirectoryAsRoot($file) {
        $command = sprintf('sudo rm %s -rf', $file);
        CDevSuite::commandLine()->run($command);

        if (is_dir($file)) {
            throw new \Exception(sprintf('Failed to remove directory "%s".', $file));
        }
    }
    protected function removeFileAsRoot($file) {
        $command = sprintf('sudo rm %s -f', $file);
        CDevSuite::commandLine()->run($command);

        if (file_exists($file)) {
            throw new \Exception(sprintf('Failed to remove file "%s".', $file));
        }
    }

    /**
     * Delete the specified file or directory with files.
     *
     * @param string $files
     * @return void
     */
    public function removeAsRoot($files) {
        $files = iterator_to_array($this->toIterator($files));
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (!file_exists($file) && !is_link($file)) {
                continue;
            }

            if (is_dir($file) && !is_link($file)) {
                $this->removeAsRoot(new \FilesystemIterator($file));

                $this->removeDirectoryAsRoot($file);
            } else {
                // https://bugs.php.net/bug.php?id=52176
                if ('\\' === DIRECTORY_SEPARATOR && is_dir($file)) {
                    $this->removeDirectoryAsRoot($file);
                } else {
                    $this->removeFileAsRoot($file);
                }
            }
        }
    }

    /**
     * Determine if the given file exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists($files) {
        foreach ($this->toIterator($files) as $file) {
            if (!file_exists($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Backup the given file.
     *
     * @param string $file
     * @return bool
     */
    public function backup($file) {
        $to = $file . '.bak';

        if (!$this->exists($to)) {
            if ($this->exists($file)) {
                return $this->rename($file, $to);
            }
        }

        return false;
    }
    
    
    /**
     * Backup the given file.
     *
     * @param string $file
     * @return bool
     */
    public function backupAsRoot($file) {
        $to = $file . '.bak';

        if (!$this->exists($to)) {
            if ($this->exists($file)) {
                return $this->renameAsRoot($file, $to);
            }
        }

        return false;
    }

    /**
     * Restore a backed up file.
     *
     * @param string $file
     * @return bool
     */
    public function restore($file) {
        $from = $file . '.bak';

        if ($this->exists($from)) {
            return $this->rename($from, $file);
        }

        return false;
    }
    
    /**
     * Restore a backed up file.
     *
     * @param string $file
     * @return bool
     */
    public function restoreAsRoot($file) {
        $from = $file . '.bak';

        if ($this->exists($from)) {
            return $this->renameAsRoot($from, $file);
        }

        return false;
    }

    /**
     * Comment a line in a file.
     *
     * @param string $line
     * @param string $file
     * @return void
     */
    public function commentLine($line, $file) {
        if ($this->exists($file)) {
            $command = "sed -i '/{$line}/ s/^/# /' {$file}";
            CDevSuite::commandLine()->run($command);
        }
    }

    /**
     * Uncomment a line in a file.
     *
     * @param string $line
     * @param string $file
     * @return void
     */
    public function uncommentLine($line, $file) {
        if ($this->exists($file)) {
            $command = "sed -i '/{$line}/ s/# *//' {$file}";
            CDevSuite::commandLine()->run($command);
        }
    }

    /**
     * Resolve the given symbolic link.
     *
     * @param string $path
     * @return string
     */
    public function readLink($path) {
        $link = $path;

        while (is_link($link)) {
            $link = readlink($link);
        }

        return $link;
    }

}
