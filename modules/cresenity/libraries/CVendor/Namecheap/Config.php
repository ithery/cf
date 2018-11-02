<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 25, 2018, 5:21:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Class CVendor_Namecheap_Config
 */
class CVendor_Namecheap_Config implements CVendor_Namecheap_Contract_ArrayableContract {

    /**
     * @var string
     */
    protected $apiUser;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $clientIp;

    /**
     * Config constructor.
     * @param string|null $apiUser
     * @param string|null $apiKey
     * @param string|null $username
     * @param string|null $clientIp
     */
    public function __construct($apiUser = null, $apiKey = null, $username = null, $clientIp = null) {
        $this->apiKey = $apiKey;
        $this->apiUser = $apiUser;
        $this->username = $username;
        $this->clientIp = $clientIp;
    }

    /**
     * @return string
     */
    public function getApiUser() {
        return $this->apiUser;
    }

    /**
     * @param string $apiUser
     * @return Config
     */
    public function setApiUser($apiUser) {
        $this->apiUser = $apiUser;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return Config
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param string $username
     * @return Config
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientIp() {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     * @return Config
     */
    public function setClientIp($clientIp) {
        $this->clientIp = $clientIp;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'ApiKey' => $this->apiKey,
            'APIUser' => $this->apiUser,
            'UserName' => $this->username,
            'ClientIp' => $this->clientIp
        ];
    }

}
