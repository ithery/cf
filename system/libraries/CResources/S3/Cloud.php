<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 29, 2018, 10:57:46 PM
 */
class CResources_S3_Cloud {
    protected $config;

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function getConfig($key) {
        return carr::get($this->config, $key);
    }
}
