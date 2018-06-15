<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 3:17:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Config {

    protected static $instance;
    protected $config;
    protected $configBefore;

    public static function instance() {
        if (self::$instance == null) {
            return new CServer_Config();
        }
        return self::$instance;
    }

    public function __construct() {

        $defaultConfig = array(
            'use_vhost' => false,
            'debug' => false,
            'load_percent_enabled' => true,
        );

        $this->config = array_merge($defaultConfig, CF::config('server', array()));
        $this->configBefore = $this->config;
    }

    public function get($key) {
        return carr::get($this->config, $key);
    }

    public function getAll() {
        return $this->config;
    }

    public function set($key, $val) {
        $this->config[$key] = $val;
        return $this;
    }

    public function reset() {
        $this->config = $this->configBefore;
    }

    public function isUseVHost() {
        return $this->get('use_vhost') === true;
    }

    public function isDebug() {
        return $this->get('debug') === true;
    }

    public function loadPercentEnabled() {
        return $this->get('load_percent_enabled') === true;
    }

}
