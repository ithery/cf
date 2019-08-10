<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 3:57:34 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CStorage_FactoryInterface {

    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return CStorage_FilesystemInterface
     */
    public function disk($name = null);
}
