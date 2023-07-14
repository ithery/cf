<?php

class CVendor_BCA {
    /**
     * @param mixed $options
     *
     * @return CVendor_BCA_Api
     */
    public static function api($options = [], CCache_Repository $cache = null) {
        return new CVendor_BCA_Api($options, $cache);
    }
}
