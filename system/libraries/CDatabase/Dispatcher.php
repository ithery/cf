<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 15, 2019, 7:07:02 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Dispatcher extends CEvent_Dispatcher {

    protected static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CEvent_Dispatcher();
        }
        return self::$instance;
    }

    public function __construct() {
        
    }

}
