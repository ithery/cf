<?php

interface CCache_Contract_DriverHaveMethodAddInterface {
    /**
     * Store an item in the cache if the key doesn't exist.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $seconds
     *
     * @return bool
     */
    public function add($key, $value, $seconds);
}
