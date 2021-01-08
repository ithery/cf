<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 15, 2019, 11:29:51 PM
 */
use LEClient\LEClient;

class CVendor_LetsEncrypt {
    /**
     * @var CVendor_LetsEncrypt
     */
    private static $instance;

    /**
     * @var LEClient\LEClient
     */
    private $client;

    /**
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
        $defaultCertificateDirectory = DOCROOT . 'certificate/letsencrypt/' . CF::domain() . '/';
        $defaultAccountDirectory = 'account/';
        $certificateKeys = carr::get($this->config, 'certificateKeys', $defaultCertificateDirectory);
        $accountKeys = carr::get($this->config, 'accountKeys', $defaultAccountDirectory);
        $this->certificateKeys = $certificateKeys;
        $client = new LEClient($email, $acmeURL, $log, $certificateKeys, $accountKeys);
        $this->client = $client;
    }

    public function removeCertificate() {
        $certificatePath = $this->certificateKeys;
        CHelper::file()->deleteDirectory($certificatePath);
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

    public function getPrivateKeyPath() {
        return $this->certificateKeys . 'private.pem';
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
        $data = [];
        $orderDataFile = $this->certificateKeys . 'orderData';
        if (!file_exists($orderDataFile)) {
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
            file_put_contents($orderDataFile, $response);
        } else {
            $response = trim(file_get_contents($orderDataFile));
            $data = json_decode($response, true);
            if (!is_array($data)) {
                return false;
            }
        }
        return $data;
    }

    /**
     * @return LEClient\LEClient
     */
    public function client() {
        return $this->client;
    }
}
