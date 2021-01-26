<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 1:29:26 PM
 */
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
