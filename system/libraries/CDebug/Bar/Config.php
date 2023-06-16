<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDebug_Bar_Config {
    protected $config = [];

    public function __construct(array $config = []) {
        $this->config = $config;
    }

    public function setOptions(array $config = []) {
        $this->config = $config;

        return $this;
    }
}
