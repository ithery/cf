<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CCache_LockProviderInterface {
    /**
     * Get a lock instance.
     *
     * @param string $name
     * @param int    $seconds
     *
     * @return CCache_LockAbstract
     */
    public function lock($name, $seconds = 0);
}
