<?php

/**
 * Description of Filesystem
 *
 * @author Hery
 */
class CDevSuite_Windows_Filesystem extends CDevSuite_Filesystem {

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

        $mode = is_dir($target) ? 'J' : 'H';

        exec("mklink /{$mode} \"{$link}\" \"{$target}\"");
    }

    /**
     * Delete the file at the given path.
     *
     * @param string $path
     *
     * @return void
     */
    public function unlink($path) {
        if ($this->isLink($path)) {
            $dir = pathinfo($path, PATHINFO_DIRNAME);
            $link = pathinfo($path, PATHINFO_BASENAME);

            if (is_dir($path)) {
                exec("cd \"{$dir}\" && rmdir {$link}");
            } else {
                @unlink($path);
            }
        } elseif (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Determine if the given path is a symbolic link.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isLink($path) {
        if (is_link($path)) {
            return true;
        }

        return $this->isDir($path) && filesize($path) === 0;
    }

    /**
     * Determine if the given path is a broken symbolic link.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isBrokenLink($path) {
        return is_link($path) || @readlink($path) === false;
    }

}
