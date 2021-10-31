<?php

class CBackup_Config {
    protected static $instance;
    protected $config;

    /**
     * @return CBackup_Config
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CBackup_Config();
        }
        return static::$instance;
    }

    private function __construct() {
        $this->reset();
    }

    public function reset() {
        $this->config = [];
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }

    public function getConfig($key, $defaultValue = null) {
        return carr::get($this->config, $key, CF::config('backup.' . $key, $defaultValue));
    }
}
