<?php

abstract class CVendor_BunnyCDN_ApiAbstract {
    /**
     * @var CVendor_BunnyCDN_ClientAbstract
     */
    protected $client;

    /**
     * @return CVendor_BunnyCDN_ClientAbstract
     */
    public function client() {
        return $this->client;
    }

    public function getGuzzleClient() {
        return $this->client()->guzzleClient();
    }
}
