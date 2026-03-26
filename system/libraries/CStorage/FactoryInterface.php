<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CStorage_FactoryInterface {
    /**
     * Get a filesystem implementation.
     *
     * @param null|string $name
     *
     * @return CStorage_FilesystemInterface
     */
    public function disk($name = null);
}
