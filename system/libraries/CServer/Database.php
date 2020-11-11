<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 1:29:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Database {

    protected $engine;
    protected static $instance;

    public static function instance() {
        if (self::$instance == null) {
            return new CServer_Php();
        }
        return self::$instance;
    }

}
