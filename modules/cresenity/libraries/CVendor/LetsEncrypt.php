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
    private $certificateKeys;

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
        $email = carr::get($this->config, 'email');
        $acmeURL = carr::get($this->config, 'acmeURL', LEClient::LE_PRODUCTION);
        $log = carr::get($this->config, 'log', LEClient::LOG_OFF);
        $defaultCertificateDirectory = DOCROOT . 'application/' . CF::appCode() . '/default/data/certificate/' . CF::domain() . '/';
        $defaultAccountDirectory = 'account/';
        $certificateKeys = carr::get($this->config, 'certificateKeys', $defaultCertificateDirectory);
        $accountKeys = carr::get($this->config, 'accountKeys', $defaultAccountDirectory);
        $this->certificateKeys = $certificateKeys;
        $client = new LEClient($email, $acmeURL, $log, $certificateKeys, $accountKeys);
        $this->client = $client;
    }

    public function haveCertificate() {
        return file_exists($this->getCertificatePath());
    }

    public function haveChain() {
        return file_exists($this->getChainPath());
    }

    public function getCertificatePath() {
        return $this->certificateKeys . 'certificate.crt';
    }

    public function getChainPath() {
        return $this->certificateKeys . 'fullchain.crt';
    }

    public function getCertificateUrl() {
        return str_replace(DOCROOT, curl::httpbase(), $this->getCertificatePath());
    }

    public function getChainUrl() {
        return str_replace(DOCROOT, curl::httpbase(), $this->getChainPath());
    }

    public function getOrderData() {
        if (!$this->haveCertificate()) {
            return false;
        }
        $orderFile = $this->certificateKeys . 'order';
        if (!file_exists($orderFile)) {
            return false;
        }
        $orderUrl = trim(file_get_contents($orderFile));
        $curl = CCurl::factory($orderUrl);
        $response = $curl->exec()->response();
        $data = json_decode($response, true);
        if (!is_array($data)) {
            return false;
        }
        return $data;
    }

    /**
     * 
     * @return LEClient\LEClient
     */
    public function client() {
        return $this->client;
    }

}
