<?php

interface CVendor_Xendit_HttpClient_HttpClientInterface {
    /**
     * Create a request to execute in _executeRequest
     *
     * @param string $method         request method
     * @param string $url            url
     * @param array  $defaultHeaders request headers
     * @param array  $params         parameters
     *
     * @return array
     *
     * @throws ApiException
     */
    public function sendRequest(
        $method,
        $url,
        array $defaultHeaders,
        $params
    );
}
