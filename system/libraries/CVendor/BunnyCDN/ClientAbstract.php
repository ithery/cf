<?php
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;

abstract class CVendor_BunnyCDN_ClientAbstract {
    protected $baseUrl;

    protected $client;

    abstract public function getBaseUrl();

    /**
     * @param mixed $path
     * @param mixed $method
     *
     * @throws GuzzleException
     */
    abstract public function request($path, $method = 'GET', array $options = []);

    public function guzzleClient() {
        if ($this->client == null) {
            $this->client = new Guzzle();
        }

        return $this->client;
    }
}
