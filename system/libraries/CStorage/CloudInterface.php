<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 3:56:39 AM
 */
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
