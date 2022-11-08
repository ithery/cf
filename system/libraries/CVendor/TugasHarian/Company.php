<?php

class CVendor_TugasHarian_Company {
    protected $token;

    protected $isSandbox;

    /**
     * @var CVendor_TugasHarian_Client
     */
    protected $client;

    public function __construct($token, $options = []) {
        $this->token = $token;
        $this->isSandbox = (bool) carr::get($options, 'sandbox');
        $this->client = new CVendor_TugasHarian_Client(new CVendor_TugasHarian_Adapter_GuzzleAdapter(['token' => $this->token]), $this->getBaseUri());
    }

    protected function getBaseUri() {
        return $this->isSandbox ? 'https://xpreneur.dev.ittron.co.id/api/company/' : 'https://tugasharian/api/company/';
    }

    public function getInfo() {
        return $this->handleResponse($this->client->get('info'));
    }

    public function handleResponse($response) {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }
        $errCode = (int) carr::get($response, 'errCode');
        if ($errCode != 0) {
            throw new CVendor_TugasHarian_Exception_ApiException(carr::get($response, 'errMessage'));
        }

        return carr::get($response, 'data', []);
    }
}
