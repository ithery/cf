<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CCache_Driver_FileDriver_EngineAbstract implements CCache_Driver_FileDriver_EngineInterface {
    protected $options;

    public function __construct($options) {
        $this->options = $options;
    }

    public function getOption($key, $default = null) {
        return carr::get($this->options, $key, $default);
    }

    public function setOption($key, $value) {
        return carr::set($this->options, $key, $value);
    }

    public function hasOption($key) {
        return $this->getOption($key) !== null;
    }
}
