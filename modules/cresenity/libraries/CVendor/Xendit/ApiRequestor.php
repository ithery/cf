<?php
class CVendor_Xendit_ApiRequestor {
    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Send request and processing response
     *
     * @param string $method  request method (get, post, patch, etc)
     * @param string $url     base url
     * @param array  $params  user's params
     * @param array  $headers user's additional headers
     *
     * @return array
     *
     * @throws Exceptions\ApiException
     */
    public function request($method, $url, $params = [], $headers = []) {
        list($rbody, $rcode, $rheaders)
            = $this->requestRaw($method, $url, $params, $headers);

        return json_decode($rbody, true);
    }

    /**
     * Set must-have headers
     *
     * @param array $headers user's headers
     *
     * @return array
     */
    private function setDefaultHeaders($headers) {
        $defaultHeaders = [];
        $lib = 'php';
        $libVersion = CVendor_Xendit_Config::getLibVersion();

        $defaultHeaders['Content-Type'] = 'application/json';
        $defaultHeaders['xendit-lib'] = $lib;
        $defaultHeaders['xendit-lib-ver'] = $libVersion;

        return array_merge($defaultHeaders, $headers);
    }

    /**
     * Send request from client
     *
     * @param string $method  request method
     * @param string $url     additional url to base url
     * @param array  $params  user's params
     * @param array  $headers request' headers
     *
     * @return array
     *
     * @throws Exceptions\ApiException
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
     * Create HTTP CLient
     *
     * @return CVendor_Xendit_HttpClient_GuzzleClient
     */
    private function httpClient() {
        return CVendor_Xendit_HttpClient_GuzzleClient::instance();
    }
}
