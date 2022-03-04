<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 5:05:33 AM
 */
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
