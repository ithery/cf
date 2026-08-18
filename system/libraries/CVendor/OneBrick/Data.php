<?php

class CVendor_OneBrick_Data {
    /**
     * @var CVendor_OneBrick_Client
     */
    protected $client;

    protected $isSandbox;

    public function __construct($baseUri, $options = []) {
        $baseUri = rtrim($baseUri, '/');
        $options['base_uri'] = $baseUri;
        $options['type'] = CVendor_OneBrick::TYPE_DATA;
        $this->isSandbox = cstr::contains($baseUri, 'sandbox');
        $this->client = new CVendor_OneBrick_Client(new CVendor_OneBrick_Adapter_GuzzleAdapter($options), $baseUri);
    }

    public function getInstitutionList() {
        return $this->handleResponse($this->client->get('institution/list'));
    }

    public function getAccountList() {
        return $this->handleResponse($this->client->get('account/list'));
    }

    public function getBrickWidgetUrl() {
        $url = 'https://cdn.onebrick.io/sandbox-widget/v1/?accessToken=' . $this->client->getAccessToken();

        return $url;
        //cdbg::dd($this->isSandbox);
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

        $status = (int) carr::get($response, 'status');
        if ($status != 200) {
            $errMessage = carr::get($response, 'message');

            throw new CVendor_OneBrick_Exception_ApiException($errMessage);
        }

        return carr::get($response, 'data', []);
    }
}
