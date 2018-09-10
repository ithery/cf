<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 24, 2018, 7:53:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CCache_RetrievesMultipleKeysTrait {

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys) {
        $return = [];

        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }

        return $return;
    }

    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * @param  array  $values
     * @param  float|int  $minutes
     * @return void
     */
    public function putMany(array $values, $minutes) {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $minutes);
        }
    }

}
