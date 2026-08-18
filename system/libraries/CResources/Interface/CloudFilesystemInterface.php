<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CResources_Interface_CloudFilesystemInterface extends CResources_Interface_FilesystemInterface {
    /**
     * Get the URL for the file at the given path.
     *
     * @param string $path
     *
     * @return string
     */
    public function url($path);
}
