<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 2:40:04 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Navigation {


    public static $instance = array();

    /**
     * 
     * @param string $appCode
     * @return CApp_Navigation
     */
    public static function instance($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        if (!isset(self::$instance[$domain])) {
            self::$instance[$domain] = new CApp_Navigation($domain);
        }
        return self::$instance[$domain];
    }

    public function __construct($domain = null) {

    }

    public function render($options = array()) {

        $engine = carr::get($options, 'engine', 'bootstrap');
        $layout = carr::get($options, 'layout', 'horizontal');

        $engineClassName = 'CApp_Navigation_Engine_' . ucfirst($engine);
        $engineClass = new $engineClassName();
        return $engineClass->render();
    }

}
