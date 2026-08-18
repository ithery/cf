<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CManager_File_ConnectorAbstract {
    /**
     * @var CManager_File_ConfigAbstract
     */
    protected $config;

    public function __construct(CManager_File_ConfigAbstract $config) {
        $this->config = $config;
    }

    public function getConfig($key = null, $default = null) {
        return $this->config->getConfig($key, $default);
    }

    abstract public function run($method = null);
}
