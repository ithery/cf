<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:21:02 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CHelper_File as File;

class CTracker_Config {

    protected static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CTracker_Config();
        }
        return static::$instance;
    }

    public function get($key) {
        return CConfig::instance('tracker')->get($key);
    }

    public function isLogDevice() {
        return true;
    }

    public function isCacheEnabled() {
        return false;
    }

}
