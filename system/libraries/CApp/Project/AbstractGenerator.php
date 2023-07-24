<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Project_AbstractGenerator {
    protected $options;

    public function __construct() {
        $this->options = [];
    }

    protected function mergeOptions($options) {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function option($key) {
        return carr::get($this->options, $key);
    }
}
