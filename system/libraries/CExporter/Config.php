<?php

class CExporter_Config {
    protected static $instance;

    protected $data;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CExporter_Config();
        }

        return static::$instance;
    }

    public function __construct() {
        $default = [];

        $this->data = CConfig::instance('exporter')->get();
        if (!is_array($this->data)) {
            $this->data = [];
        }
        $this->data = array_merge($default, $this->data);
    }

    public function get($key, $default = null) {
        return carr::get($this->data, $key, $default);
    }

    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    public function set($key, $value) {
        $this->data[$key] = $value;

        return $this;
    }
}
