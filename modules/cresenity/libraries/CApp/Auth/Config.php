<?php

class CApp_Auth_Config {
    private static $instance;

    public static function instance($type = null) {
        if (static::$instance == null) {
            static::$instance = [];
        }
        if (!isset(static::$instance[$type])) {
            static::$instance[$type] = new CApp_Auth_Config($type);
        }
        return static::$instance[$type];
    }

    public function __construct($type) {
        if ($type == null) {
            $type = CF::config('app.auth.default');
        }
    }
}
