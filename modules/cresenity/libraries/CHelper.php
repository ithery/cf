<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:51:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CHelper {

    private static $helper;
    private static $instance;

    private static function instance() {
        if (self::$instance == null) {
            self::$instance = new CHelper();
        }
        return self::$instance;
    }

    /**
     * 
     * @return \CHelper_File
     */
    public static function file() {

        self::$helper = 'File';
        return self::instance();
    }

    /**
     * 
     * @return \CHelper_Formatter
     */
    public static function formatter() {
        self::$helper = 'Formatter';
        return self::instance();
    }

    /**
     * 
     * @return \CHelper_Base64
     */
    public static function base64() {
        self::$helper = 'Base64';
        return self::instance();
    }

    public function __call($method, $args) {
        $helperClass = 'CHelper_' . self::$helper;
        return call_user_func_array(array($helperClass, $method), $args);
    }

}
