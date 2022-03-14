<?php
/**
 * @see CAnalytics_Google_AnalyticGA4
 */
class CAnalytics_Google_AnalyticGA4_ServerClient {
    /**
     * @var string
     */
    private $measurementId = '';

    /**
     * @var string
     */
    private $clientId = '';

    /**
     * Undocumented variable.
     *
     * @var string
     */
    private $apiSecret = '';

    /**
     * @var bool
     */
    private $debugging = false;

    private $userId = null;

    private $userProperties = null;

    public function __construct($measurementId, $apiSecret) {
        $this->measurementId = $measurementId;
        $this->apiSecret = $apiSecret;
    }

    /**
     * @param string $clientId
     *
     * @return self
     */
    public function setClientId($clientId) {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getClientId() {
        return $this->clientId;
    }

    /**
     * @return self
     */
    public function enableDebugging() {
        $this->debugging = true;

        return $this;
    }

    /**
     * @param array $eventData
     *
     * @return array
     */
    public function postEvent(array $eventData) {
        $payload = [];
        $payload['client_id'] = $this->clientId;
        $payload['events'] = [$eventData];
        if ($this->userId) {
            $payload['user_id'] = $this->userId;
        }
        if ($this->userProperties) {
            $payload['user_properties'] = c::collect($this->userProperties)->mapWithKeys(function ($value, $key) {
                return [$key => ['value' => $value]];
            })->toArray();
        }

        $response = CHTTP::client()->withOptions([
            'query' => [
                'measurement_id' => $this->measurementId,
                'api_secret' => $this->apiSecret,
            ],
        ])->post($this->getRequestUrl(), $payload);

        if ($this->debugging) {
            return $response->json();
        }

        return [
            'status' => $response->successful()
        ];
    }

    private function getRequestUrl() {
        $url = 'https://www.google-analytics.com';
        $url .= $this->debugging ? '/debug' : '';

        return $url . '/mp/collect';
    }

    /**
     * @param string     $userId
     * @param null|array $userProperties
     *
     * @return $this
     */
    public function withUser($userId, $userProperties = null) {
        $this->userId = $userId;
        $this->userProperties = $userProperties;

        return $this;
    }
}
