<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 3:57:34 AM
 */
interface CStorage_FactoryInterface {
    /**
     * Get a filesystem implementation.
     *
     * @param string|null $name
     *
     * @return CStorage_FilesystemInterface
     */
    public function disk($name = null);
}
