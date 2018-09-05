<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 1, 2018, 12:57:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Navigation_Data {

    protected static $navigationCallback = array();

    public static function get($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        $navFile = CF::get_file('config', 'nav', $domain);

        $data = null;
        if ($navFile != null) {
            $data = include $navFile;
        }
         
        if (isset(self::$navigationCallback[$domain]) && self::$navigationCallback[$domain] != null && is_callable(self::$navigationCallback[$domain])) {
           
            $data = call_user_func(self::$navigationCallback[$domain], $data);
        }
        return $data;
    }

    public static function setNavigationCallback(callable $navigationCallback, $domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
       
        self::$navigationCallback[$domain] = $navigationCallback;
    }

}
