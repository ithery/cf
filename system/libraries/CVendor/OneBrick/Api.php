<?php

class CVendor_OneBrick_Api {
    /**
     * @var CVendor_OneBrick_Client
     */
    protected $client;

    public function __construct($options = []) {
        $baseUri = carr::get($options, 'base_uri', CF::isProduction() ? 'https://api.onebrick.io/v2/' : 'https://sandbox.onebrick.io/v2/');
        $options['base_uri'] = rtrim($baseUri, '/');
        $this->client = new CVendor_OneBrick_Client(new CVendor_OneBrick_Adapter_GuzzleAdapter($options), $baseUri);
    }

    public function getInstitutionList() {
        return $this->handleResponse($this->client->get('institution/list'));
    }

    /**
     * @param mixed $response
     *
     * @return array
     */
    public function handleResponse($response) {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }
        $errCode = (int) carr::get($response, 'errCode');
        if ($errCode != 0) {
            $errMessage = carr::get($response, 'errMessage');

            throw new CVendor_Wago_Exception_ApiException($errMessage);
        }

        return carr::get($response, 'data', []);
    }
}
