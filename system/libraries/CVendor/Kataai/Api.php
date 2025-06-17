<?php

class CVendor_Kataai_Api {
    protected $client;

    public function __construct(CVendor_Kataai_Client $client) {
        $this->client = $client;
    }

    /**
     * Retrieves the login data from the client.
     *
     * @return array the login data
     */
    public function getLoginData() {
        return $this->client->getLoginData();
    }

    public function getApiUrl($path) {
        return rtrim($this->client->getBaseUrl(), '/') . '/' . ltrim($path, '/');
    }

    public function sendTextMessage($to, $text) {
        $params = [
            'to' => $to,
            'recipient_type' => 'individual',
            'type' => 'text',
            'text' => [
                'body' => $text
            ]
        ];

        return $this->sendMessage($params);
    }

    public function sendMessage($options = []) {
        $path = 'v1/messages';
        $url = $this->getApiUrl($path);

        $response = $this->client->post($url, $options);

        return $this->handleResponse($response);
    }

    protected function handleResponse($response) {
        $json = json_decode($response, true);
        //check is json successfully decoded
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CVendor_Kataai_Exception_ApiException('JSON Error: ' . json_last_error_msg());
        }

        return $json;
    }
}
