<?php

class CVendor_BCA_Api {
    /**
     * API url.
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * Application client Id.
     *
     * @var string
     */
    protected $clientId;

    /**
     * Application client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * CorporateId.
     *
     * @var string
     */
    protected $corporateId;

    /**
     * @var CCache_Repository
     */
    protected $cache;

    /**
     * ApiKey.
     *
     * @var mixed
     */
    private $apiKey;

    /**
     * ApiSecret.
     *
     * @var mixed
     */
    private $apiSecret;

    /**
     * ServicePath.
     *
     * @var string
     */
    private $servicePath = 'CVendor_BCA_Service_';

    /**
     * Init.
     *
     * @param mixed      $options
     * @param null|mixed $cache
     *
     * @return void
     */
    public function __construct($options, CCache_Repository $cache = null) {
        $this->apiUrl = carr::get($options, 'api_url', CF::config('vendor.bca.api_url', 'https://sandbox.bca.co.id:443'));
        $this->clientId = carr::get($options, 'client_id', CF::config('vendor.bca.client_id', '07594711-5ba6-460e-a36f-64ac63ade5c8'));
        $this->clientSecret = carr::get($options, 'client_secret', CF::config('vendor.bca.client_secret', '5df114de-8771-405d-af55-08a489f80a40'));
        $this->apiKey = carr::get($options, 'api_key', CF::config('vendor.bca.api_key', 'e2939aac-6ba3-4fa4-b97d-384bf52bc166'));
        $this->apiSecret = carr::get($options, 'api_secret', CF::config('vendor.bca.api_secret', 'f16b186a-2a96-4018-a2ad-040bf21aa76b'));
        $this->corporateId = carr::get($options, 'corporate_id', CF::config('vendor.bca.corporate_id', 'BCAAPI2016'));

        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function getApiUrl() {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getApiSecret() {
        return $this->apiSecret;
    }

    /**
     * @return string
     */
    public function getClientId() {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getCorporateId() {
        return $this->corporateId;
    }

    public function getCacheKey($key) {
        $groupKey = md5(implode('|', [
            $this->apiUrl,
            $this->apiKey,
            $this->apiSecret,
            $this->clientId,
            $this->clientSecret,
        ]));

        return 'vendor:bca:' . $groupKey . ':' . $key;
    }

    /**
     * @return string
     */
    public function getClientSecret() {
        return $this->clientSecret;
    }

    public function getAccessToken() {
        $accessToken = null;

        if ($this->cache) {
            $accessToken = $this->cache->get($this->getCacheKey('access_token'));
        }
        if ($accessToken == null) {
            $tokenResponse = $this->authentication()->accessToken();
            $accessToken = carr::get($tokenResponse, 'access_token');
            if ($this->cache && $accessToken) {
                $ttl = carr::get($tokenResponse, 'expires_in');

                $this->cache->put($this->getCacheKey('access_token'), $accessToken, $ttl);
            }
        }

        return $accessToken;
    }

    /**
     * @return CVendor_BCA_Service_AuthenticationService
     */
    public function authentication() {
        return $this->service('Authentication');
    }

    /**
     * @param mixed $token
     *
     * @return CVendor_BCA_Service_BusinessBankingService
     */
    public function businessBanking($token) {
        return $this->service('BusinessBanking', $token);
    }

    /**
     * Dynamiclly bind class.
     *
     * @param string     $serviceName
     * @param null|mixed $token
     *
     * @return \CVendor_BCA_ServiceAbstract
     */
    private function service($serviceName, $token = null) {
        $service = $this->servicePath . $serviceName . 'Service';

        return new $service($this, $token);
    }
}
