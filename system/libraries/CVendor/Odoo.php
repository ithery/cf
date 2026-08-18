<?php

defined('SYSPATH') or die('No direct access allowed.');

use CVendor_Odoo_Helper as Helper;

class CVendor_Odoo {
    /**
     * Get a client.
     *
     * @param array $config
     *
     * @return CVendor_Odoo_Client
     */
    public static function getClient($config) {
        return new CVendor_Odoo_Client($config);
    }
}
