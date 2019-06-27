<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 25, 2019, 8:27:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CVendor_Odoo_Helper as Helper;

class CVendor_Odoo {

    /**
     * Get a client 
     *
     * @param string $config
     * @return CVendor_Odoo_Client
     */
    public static function getClient($config) {
        return new CVendor_Odoo_Client($config);
    }

}
