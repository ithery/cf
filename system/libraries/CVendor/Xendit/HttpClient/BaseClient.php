<?php

class CVendor_Xendit_HttpClient_BaseClient {
    protected $baseUri;

    protected $apiKey;

    /**
     * BaseClient constructor
     *
     * @param string      $apiKey
     * @param null|string $baseUri
     */
    public function __construct($apiKey, $baseUri) {
        $this->baseUri = $baseUri;
        $this->apiKey = $apiKey;
    }
}
