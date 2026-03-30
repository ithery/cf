<?php

defined('SYSPATH') or die('No direct access allowed.');

class CResources_S3_Cloud {
    protected $config;

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function getConfig($key) {
        return carr::get($this->config, $key);
    }
}
