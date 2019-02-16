<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:06:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache {

    protected static $instance;

    /**
     *
     * @var CCache_Repository
     */
    protected $repository;

    /**
     * options for this cache
     * 
     * @var array
     */
    protected $options;

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
        $this->options = $options;
        $driverName = carr::get($options, 'driver', 'Null');
        $driverOption = carr::get($options, 'options', array());
        $driverClass = 'CCache_Driver_' . $driverName . 'Driver';
        $driver = new $driverClass($driverOption);
        $this->repository = new CCache_Repository($driver);
    }

    public function __call($method, $args) {
        return call_user_func_array(array($this->repository, $method), $args);
    }

}
