<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 27, 2019, 2:49:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CManager_File_ConnectorAbstract {

    /**
     *
     * @var CManager_File_ConfigAbstract 
     */
    protected $config;

    public function __construct(CManager_File_ConfigAbstract $config) {
        $this->config = $config;
    }

    public function getConfig($key = null, $default = null) {
        return $this->config->getConfig($key, $default);
    }

}
