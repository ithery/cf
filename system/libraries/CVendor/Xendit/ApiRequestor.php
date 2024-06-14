<?php
class CVendor_Xendit_ApiRequestor {
    protected $httpClient;

    protected $libVersion;

    public function __construct($secretApiKey, $baseUri, $libVersion = null) {
        $this->httpClient = new CVendor_Xendit_HttpClient_GuzzleClient($secretApiKey, $baseUri);
        if ($libVersion == null) {
            $libVersion = CVendor_Xendit::VERSION;
        }
        $this->libVersion = $libVersion;
    }

    /**
     * Send request and processing response.
     *
     * @param string $method  request method (get, post, patch, etc)
     * @param string $url     base url
     * @param array  $params  user's params
     * @param array  $headers user's additional headers
     *
     * @throws CVendor_Xendit_Exception_ApiException
     *
     * @return array
     */
    public function request($method, $url, $params = [], $headers = []) {
        list($rbody, $rcode, $rheaders)
            = $this->requestRaw($method, $url, $params, $headers);

        return json_decode($rbody, true);
    }

    /**
     * Set must-have headers.
     *
     * @param array $headers user's headers
     *
     * @return array
     */
    private function setDefaultHeaders($headers) {
        $defaultHeaders = [];
        $lib = 'php';

        $defaultHeaders['Content-Type'] = 'application/json';
        $defaultHeaders['xendit-lib'] = $lib;
        $defaultHeaders['xendit-lib-ver'] = $this->libVersion;

        return array_merge($defaultHeaders, $headers);
    }

    /**
     * Send request from client.
     *
     * @param string $method  request method
     * @param string $url     additional url to base url
     * @param array  $params  user's params
     * @param array  $headers request' headers
     *
     * @throws Exceptions\ApiException
     *
     * @return array
     */
    private function requestRaw($method, $url, $params, $headers) {
        $defaultHeaders = $this->setDefaultHeaders($headers);

        $response = $this->httpClient()->sendRequest(
            $method,
            $url,
            $defaultHeaders,
            $params
        );

        $rbody = $response[0];
        $rcode = $response[1];
        $rheaders = $response[2];

        return [$rbody, $rcode, $rheaders];
    }

    /**
     * Create HTTP CLient.
     *
     * @return CVendor_Xendit_HttpClient_GuzzleClient
     */
    private function httpClient() {
        return $this->httpClient;
    }
}
