<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CManager_File_ConfigAbstract {
    protected $options;

    public function __construct($options) {
        $this->options = $options;
    }

    public function getConfig($key = null, $default = null) {
        if ($key == null) {
            return $this->options;
        }

        return carr::get($this->options, $key, $default);
    }
}
