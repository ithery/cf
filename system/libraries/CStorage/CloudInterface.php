<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 3:56:39 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CStorage_CloudInterface extends CStorage_FilesystemInterface {

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function url($path);
}
