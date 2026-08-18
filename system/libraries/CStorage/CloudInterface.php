<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CStorage_CloudInterface extends CStorage_FilesystemInterface {
    /**
     * Get the URL for the file at the given path.
     *
     * @param string $path
     *
     * @return string
     */
    public function url($path);
}
