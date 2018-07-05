<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 29, 2018, 10:57:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CResources_S3_Cloud {

    protected $config;

    public function __construct($config = array()) {
        $this->config = $config;
    }

    public function getConfig($key) {
        return carr::get($this->config, $key);
    }

}
