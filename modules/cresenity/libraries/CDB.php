<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:14:11 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDB {

    /**
     * Returns a singleton instance of Database.
     * 
     * @param string $domain
     * @param string $name
     * @param mixed $config
     * @return CDatabase
     */
    public static function database($name = 'default', $config = NULL,$domain=null) {
        return CDatabase::instance($name, $config,$domain);
    }

    
}
