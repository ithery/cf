<?php

class CVendor_Wago_Device {
    protected $token;

    protected $isSandbox;

    /**
     * @var CVendor_Wago_Client
     */
    protected $client;

    public function __construct($token, $options = []) {
        $this->token = $token;
        $this->isSandbox = (bool) carr::get($options, 'sandbox');
        $this->client = new CVendor_Wago_Client(new CVendor_Wago_Adapter_GuzzleAdapter(['token' => $this->token]), $this->getBaseUri());
    }

    protected function getBaseUri() {
        return $this->isSandbox ? 'https://wapro.dev.ittron.co.id/api/device/' : 'https://wa-go.id/api/device/';
    }

    /**
     * @param string $phone
     * @param string $message
     * @param array  $options
     *
     * @throws CVendor_Wago_Exception_ApiException
     *
     * @return array
     */
    public function sendMessage($phone, $message, array $options = []) {
        $request = [
            'phone' => $phone,
            'message' => $message,
        ];
        $imageUrl = carr::get($options, 'imageUrl');
        $scheduleAt = carr::get($options, 'scheduleAt');
        if ($imageUrl) {
            $request['imageUrl'] = $imageUrl;
        }
        if ($scheduleAt) {
            $request['scheduleAt'] = $scheduleAt;
        }

        return $this->handleResponse($this->client->post('message/send', $request));
    }

    public function getInfo() {
        return $this->handleResponse($this->client->get('info'));
    }

    public function getWebhook() {
        return $this->handleResponse($this->client->get('webhook/get'));
    }

    public function unsetWebhook() {
        return $this->handleResponse($this->client->post('webhook/unset'));
    }

    public function setWebhook($url) {
        $options = [
            'endpoint' => $url
        ];

        return $this->handleResponse($this->client->post('webhook/set', $options));
    }

    public function handleResponse($response) {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }
        $errCode = (int) carr::get($response, 'errCode');
        if ($errCode != 0) {
            throw new CVendor_Wago_Exception_ApiException(carr::get($response, 'errMessage'));
        }

        return carr::get($response, 'data', []);
    }
}
