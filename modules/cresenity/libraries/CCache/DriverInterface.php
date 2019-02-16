<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:07:23 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CCache_DriverInterface {

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key);

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  float|int  $minutes
     * @return void
     */
    public function set($key, $value, $minutes);
}
