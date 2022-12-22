<?php

class CVendor_Watzap_Device {
    protected $apiKey;

    protected $numberKey;

    /**
     * @var CVendor_Watzap_Client
     */
    protected $client;

    public function __construct($numberKey, $apiKey, $options = []) {
        $this->apiKey = $apiKey;
        $this->numberKey = $numberKey;
        $this->client = new CVendor_Watzap_Client(new CVendor_Watzap_Adapter_GuzzleAdapter(['apiKey' => $this->apiKey]), $this->getBaseUri());
    }

    protected function getBaseUri() {
        return 'https://api.watzap.id/v1/';
    }

    public function getStatus() {
        $path = 'checking_key';

        return $this->handleResponse($this->client->post($path));
    }

    public function getGroups() {
        $path = 'groups';
        $options = [
            'number_key' => $this->numberKey,
        ];

        return $this->handleResponse($this->client->post($path, $options));
    }

    /**
     * @param string $phone
     * @param string $message
     *
     * @throws CVendor_Watzap_Exception_ApiException
     *
     * @return array
     */
    public function sendMessage($phone, $message) {
        $path = 'send_message';
        $options = [
            'number_key' => $this->numberKey,
            'phone_no' => $phone,
            'message' => $message,
        ];

        return $this->handleResponse($this->client->post($path, $options));
    }

    /**
     * @param string $groupId
     * @param string $message
     * @param mixed  $retryCount
     *
     * @throws CVendor_Watzap_Exception_ApiException
     *
     * @return array
     */
    public function groupSendMessage($groupId, $message, $retryCount = 0) {
        $path = 'send_message_group';
        $options = [
            'number_key' => TWWatzap::numberKey(),
            'group_id' => $groupId,
            'message' => $message,
        ];

        return $this->handleResponse($this->client->post($path, $options));
    }

    public function validateNumber($phone) {
        $path = 'validate_number';
        $options = [
            'number_key' => $this->numberKey,
            'phone_no' => $phone,
        ];

        return $this->handleResponse($this->client->post($path, $options));
    }

    public function getWebhook() {
        $path = 'get_webhook';
        $options = [
            'number_key' => $this->numberKey,
        ];

        return $this->handleResponse($this->client->post($path, $options));
    }

    public function setWebhook($endpointUrl) {
        $path = 'set_webhook';
        $options = [
            'number_key' => $this->numberKey,
            'endpoint_url' => $endpointUrl,
        ];

        return $this->handleResponse($this->client->post($path, $options));
    }

    public function unsetWebhook() {
        $path = 'set_webhook';
        $options = [
            'number_key' => $this->numberKey,
        ];

        return $this->handleResponse($this->client->post($path, $options));
    }

    /**
     * @param string|array $response
     *
     * @throws CVendor_Watzap_Exception_ApiException
     *
     * @return array
     */
    public function handleResponse($response) {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }
        $status = (int) carr::get($response, 'status');
        if ($status != 200) {
            throw new CVendor_Watzap_Exception_ApiException(carr::get($response, 'message'));
        }

        return $response;
    }
}
