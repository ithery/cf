<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 2:40:04 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Navigation {

    protected $navs;
    public static $instance = array();

    public static function instance($appCode = null) {
        if ($appCode == null) {
            $appCode = CF::app_code();
        }
        if (!isset(self::$instance[$appCode])) {
            self::$instance[$appCode] = new CApp_Navigation($appCode);
        }
        return self::$instance[$appCode];
    }

    public function __construct($appCode = null) {

        if ($appCode == null) {
            $appCode = CF::app_code();
        }

        $path = '';
        $temp_path = '';
        $org_code = CF::org_code();
        $navFile = CF::get_file('config', 'nav');

        $this->navs = null;
        if ($navFile != null) {
            $this->navs = include $navFile;
        }
    }

    public function getArray() {
        return $this->navs;
    }

    public function navs() {
        return $this->getArray();
    }

    public function render($options = array()) {

        $engine = carr::get($options, 'engine', 'bootstrap');
        $layout = carr::get($options, 'layout', 'horizontal');

        $engineClassName = 'CApp_Navigation_Engine_' . ucfirst($engine);
        $engineClass = new $engineClassName();
        return $engineClass->render();
    }

}
