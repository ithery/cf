<?php

class CVendor_Cloudflare_Api {
    /**
     * @var \Cloudflare\API\Auth\APIKey
     */
    protected $key;

    /**
     * @var \Cloudflare\API\Adapter\Guzzle
     */
    protected $adapter;

    public function __construct($email, $apiKey) {
        $this->key = new Cloudflare\API\Auth\APIKey($email, $apiKey);
        $this->adapter = new Cloudflare\API\Adapter\Guzzle($this->key);
    }

    /**
     * @return \Cloudflare\API\Adapter\Guzzle
     */
    public function getAdapter() {
        return $this->adapter;
    }

    /**
     * @return \Cloudflare\API\Endpoints\User
     */
    public function user() {
        return new \Cloudflare\API\Endpoints\User($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\Accounts
     */
    public function accounts() {
        return new \Cloudflare\API\Endpoints\Accounts($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\Certificates
     */
    public function certificates() {
        return new \Cloudflare\API\Endpoints\Certificates($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\DNS
     */
    public function dns() {
        return new \Cloudflare\API\Endpoints\DNS($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\Firewall
     */
    public function firewall() {
        return new \Cloudflare\API\Endpoints\Firewall($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\SSL
     */
    public function ssl() {
        return new \Cloudflare\API\Endpoints\SSL($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\TLS
     */
    public function tls() {
        return new \Cloudflare\API\Endpoints\TLS($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\Pools
     */
    public function pools() {
        return new \Cloudflare\API\Endpoints\Pools($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\IPs
     */
    public function ips() {
        return new \Cloudflare\API\Endpoints\IPs($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\Crypto
     */
    public function crypto() {
        return new \Cloudflare\API\Endpoints\Crypto($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\CustomHostnames
     */
    public function customHostnames() {
        return new \Cloudflare\API\Endpoints\CustomHostnames($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\LoadBalancers
     */
    public function loadBalancers() {
        return new \Cloudflare\API\Endpoints\LoadBalancers($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\DNSAnalytics
     */
    public function dnsAnalytics() {
        return new \Cloudflare\API\Endpoints\DNSAnalytics($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\Zones
     */
    public function zones() {
        return new \Cloudflare\API\Endpoints\Zones($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\ZoneSubscriptions
     */
    public function zoneSubscriptions() {
        return new \Cloudflare\API\Endpoints\ZoneSubscriptions($this->adapter);
    }

    /**
     * @return \Cloudflare\API\Endpoints\ZoneSettings
     */
    public function zoneSettings() {
        return new \Cloudflare\API\Endpoints\ZoneSettings($this->adapter);
    }
}
