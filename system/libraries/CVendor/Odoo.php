<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 25, 2019, 8:27:59 PM
 */
use CVendor_Odoo_Helper as Helper;

class CVendor_Odoo {
    /**
     * Get a client
     *
     * @param array $config
     *
     * @return CVendor_Odoo_Client
     */
    public static function getClient($config) {
        return new CVendor_Odoo_Client($config);
    }
}
