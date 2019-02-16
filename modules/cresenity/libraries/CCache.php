<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:06:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache {

    protected static $instance;

    public static function instance($options = array()) {
        $defaultOptions = array(
            'driver' => 'array',
        );

        $options = array_merge($defaultOptions, $options);

        $instanceKey = carr::hash($options);
        if (!isset(self::$instance[$instanceKey])) {
            self::$instance[$instanceKey] = new CCache($options);
        }
        return self::$instance[$instanceKey];
    }

    private function __construct($options) {
        $driver = carr::get($options, 'driver', 'array');
        switch ($driver) {
            
        }
    }

}
