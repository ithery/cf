<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 15, 2019, 11:29:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use LEClient\LEClient;

class CVendor_LetsEncrypt {

    /**
     *
     * @var CVendor_LetsEncrypt 
     */
    private static $instance;

    /**
     *
     * @var LEClient\LEClient 
     */
    private $client;

    /**
     *
     * @var array
     */
    private $config;

    public static function instance($config = null) {
        if (self::$instance == null) {
            self::$instance = new CVendor_LetsEncrypt($config);
        }
        return self::$instance;
    }

    private function __construct($config = null) {
        if ($config == null) {
            $config = CF::config('vendor.letsEncrypt');
        }
        $this->setConfig($config);
    }

    public function setConfig($config) {
        $this->config = $config;
        $this->resetClient();
    }

    protected function resetClient() {
        $email = carr::get($config, 'email');
        $acmeURL = carr::get($config, 'acmeURL', LEClient::LE_PRODUCTION);
        $log = carr::get($config, 'log', LEClient::LOG_OFF);
        $certificateKeys = carr::get($config, 'certificateKeys', 'keys/');
        $accountKeys = carr::get($config, 'accountKeys', '__account/');

        $client = new LEClient($email, $acmeURL, $log, $certificateKeys, $accountKeys);
        $this->client = $client;
    }

}
