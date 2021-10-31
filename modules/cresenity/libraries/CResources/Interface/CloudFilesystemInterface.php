<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 1:58:54 AM
 */

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
