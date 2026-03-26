<?php

defined('SYSPATH') or die('No direct access allowed.');

class CVendor_Namecheap {
    /**
     * @param type $options
     *
     * @return \CVendor_Namecheap_Api
     */
    public static function api($options) {
        return new CVendor_Namecheap_Api($options);
    }
}
