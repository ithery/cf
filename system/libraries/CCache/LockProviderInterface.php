<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 5:05:33 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CCache_LockProviderInterface {

    /**
     * Get a lock instance.
     *
     * @param  string  $name
     * @param  int  $seconds
     * @return CCache_LockAbstract
     */
    public function lock($name, $seconds = 0);
}
