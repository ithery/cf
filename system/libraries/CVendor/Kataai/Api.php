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

    private function makeRequestComponent(CVendor_Kataai_Message $message): array {
        return CVendor_Kataai_MessageUtil::makeRequestComponent($message);
    }

    public function send(string $templateName, CVendor_Kataai_Message $message): CVendor_Kataai_Response {
        $path = 'v1/messages';
        $url = $this->getApiUrl($path);

        $options = [
            'to' => $message->getReceiver()->getTo(),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'policy' => 'deterministic',
                    'code' => 'id',
                ],
                'components' => $this->makeRequestComponent($message),
            ],
        ];

        $response = $this->client->post($url, $options);

        return $this->handleResponseMessage($response);
    }

    public function sendMessage($options = []) {
        $path = 'v1/messages';
        $url = $this->getApiUrl($path);

        $response = $this->client->post($url, $options);

        return $this->handleResponse($response);
    }

    public function getMessageTemplates($page = 1, $limit = 5, $fields = null) {
        $path = 'v1/message_templates';
        $url = $this->getApiUrl($path);
        $query = [
            'page' => $page,
            'limit' => $limit,
        ];
        if ($fields !== null) {
            if (is_array($fields)) {
                $fields = implode(',', $fields);
            }
            $query['fields'] = $fields;
        }
        $response = $this->client->get($url, $query);

        return $this->handleResponse($response);
    }

    protected function handleResponse($response, CVendor_Kataai_Message $message = null) {
        $json = json_decode($response, true);
        //check is json successfully decoded
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CVendor_Kataai_Exception_ApiException('JSON Error: ' . json_last_error_msg());
        }

        return $json;
    }

    protected function handleResponseMessage($response, CVendor_Kataai_Message $message = null) {
        $json = json_decode($response, true);
        //check is json successfully decoded
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CVendor_Kataai_Exception_ApiException('JSON Error: ' . json_last_error_msg());
        }

        return new CVendor_Kataai_Response(
            $json['messages'][0]['id'] ?? '',
            $json['messages'][0]['status'] ?? '',
            $json['messages'][0] ?? []
        );
    }
}
