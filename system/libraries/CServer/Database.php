<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Database {
    protected $engine;

    protected static $instance;

    public static function instance() {
        if (self::$instance == null) {
            return new CServer_Database();
        }

        return self::$instance;
    }
}
