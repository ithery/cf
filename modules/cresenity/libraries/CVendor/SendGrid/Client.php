<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Class Client
 * @package SendGrid
 * @version 3.9.5
 * 
 * Quickly and easily access any REST or REST-like API.
 *
 * @method CVendor_SendGrid_Response get($body = null, $query = null, $headers = null)
 * @method CVendor_SendGrid_Response post($body = null, $query = null, $headers = null)
 * @method CVendor_SendGrid_Response patch($body = null, $query = null, $headers = null)
 * @method CVendor_SendGrid_Response put($body = null, $query = null, $headers = null)
 * @method CVendor_SendGrid_Response delete($body = null, $query = null, $headers = null)
 *
 * @method CVendor_SendGrid_Client version($value)
 * @method CVendor_SendGrid_Client|Response send()
 *
 * Adding all the endpoints as a method so code completion works
 *
 * General
 * @method CVendor_SendGrid_Client stats()
 * @method CVendor_SendGrid_Client search()
 * @method CVendor_SendGrid_Client monthly()
 * @method CVendor_SendGrid_Client sums()
 * @method CVendor_SendGrid_Client monitor()
 * @method CVendor_SendGrid_Client test()
 *
 * Access settings
 * @method CVendor_SendGrid_Client access_settings()
 * @method CVendor_SendGrid_Client activity()
 * @method CVendor_SendGrid_Client whitelist()
 *
 * Alerts
 * @method CVendor_SendGrid_Client alerts()
 *
 * Api keys
 * @method CVendor_SendGrid_Client api_keys()
 *
 * ASM
 * @method CVendor_SendGrid_Client asm()
 * @method CVendor_SendGrid_Client groups()
 *
 * Browsers
 * @method CVendor_SendGrid_Client browsers()
 *
 * Campaigns
 * @method CVendor_SendGrid_Client campaigns()
 * @method CVendor_SendGrid_Client schedules()
 * @method CVendor_SendGrid_Client now()
 *
 * Categories
 * @method CVendor_SendGrid_Client categories()
 *
 * Clients
 * @method CVendor_SendGrid_Client clients()
 *
 * ContactDB
 * @method CVendor_SendGrid_Client contactdb()
 * @method CVendor_SendGrid_Client custom_fields()
 * @method CVendor_SendGrid_Client lists()
 * @method CVendor_SendGrid_Client recipients()
 * @method CVendor_SendGrid_Client billable_count()
 * @method CVendor_SendGrid_Client count()
 * @method CVendor_SendGrid_Client reserved_fields()
 * @method CVendor_SendGrid_Client segments()
 *
 * Devices
 * @method CVendor_SendGrid_Client devices()
 *
 * Geo
 * @method CVendor_SendGrid_Client geo()
 *
 * Ips
 * @method CVendor_SendGrid_Client ips()
 * @method CVendor_SendGrid_Client assigned()
 * @method CVendor_SendGrid_Client pools()
 * @method CVendor_SendGrid_Client warmup()
 *
 * Mail
 * @method CVendor_SendGrid_Client mail()
 * @method CVendor_SendGrid_Client batch()
 *
 * Mailbox Providers
 * @method CVendor_SendGrid_Client mailbox_providers()
 *
 * Mail settings
 * @method CVendor_SendGrid_Client mail_settings()
 * @method CVendor_SendGrid_Client address_whitelist()
 * @method CVendor_SendGrid_Client bcc()
 * @method CVendor_SendGrid_Client bounce_purge()
 * @method CVendor_SendGrid_Client footer()
 * @method CVendor_SendGrid_Client forward_bounce()
 * @method CVendor_SendGrid_Client forward_spam()
 * @method CVendor_SendGrid_Client plain_content()
 * @method CVendor_SendGrid_Client spam_check()
 * @method CVendor_SendGrid_Client template()
 *
 * Partner settings
 * @method CVendor_SendGrid_Client partner_settings()
 * @method CVendor_SendGrid_Client new_relic()
 *
 * Scopes
 * @method CVendor_SendGrid_Client scopes()
 *
 * Senders
 * @method CVendor_SendGrid_Client senders()
 * @method CVendor_SendGrid_Client resend_verification()
 *
 * Sub Users
 * @method CVendor_SendGrid_Client subusers()
 * @method CVendor_SendGrid_Client reputations()
 *
 * Supressions
 * @method CVendor_SendGrid_Client suppressions()
 * @method CVendor_SendGrid_Client global()
 * @method CVendor_SendGrid_Client blocks()
 * @method CVendor_SendGrid_Client bounces()
 * @method CVendor_SendGrid_Client invalid_emails()
 * @method CVendor_SendGrid_Client spam_reports()
 * @method CVendor_SendGrid_Client unsubcribes()
 *
 * Templates
 * @method CVendor_SendGrid_Client templates()
 * @method CVendor_SendGrid_Client versions()
 * @method CVendor_SendGrid_Client activate()
 *
 * Tracking settings
 * @method CVendor_SendGrid_Client tracking_settings()
 * @method CVendor_SendGrid_Client click()
 * @method CVendor_SendGrid_Client google_analytics()
 * @method CVendor_SendGrid_Client open()
 * @method CVendor_SendGrid_Client subscription()
 *
 * User
 * @method CVendor_SendGrid_Client user()
 * @method CVendor_SendGrid_Client account()
 * @method CVendor_SendGrid_Client credits()
 * @method CVendor_SendGrid_Client email()
 * @method CVendor_SendGrid_Client password()
 * @method CVendor_SendGrid_Client profile()
 * @method CVendor_SendGrid_Client scheduled_sends()
 * @method CVendor_SendGrid_Client enforced_tls()
 * @method CVendor_SendGrid_Client settings()
 * @method CVendor_SendGrid_Client username()
 * @method CVendor_SendGrid_Client webhooks()
 * @method CVendor_SendGrid_Client event()
 * @method CVendor_SendGrid_Client parse()
 *
 * Missed any? Simply add them by doing: @method Client method()
 */
class CVendor_SendGrid_Client {

    const TOO_MANY_REQUESTS_HTTP_CODE = 429;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var array
     */
    protected $path;

    /**
     * @var array
     */
    protected $curlOptions;

    /**
     * @var bool
     */
    protected $isConcurrentRequest;

    /**
     * @var array
     */
    protected $savedRequests;

    /**
     * @var bool
     */
    protected $retryOnLimit;

    /**
     * These are the supported HTTP verbs
     *
     * @var array
     */
    private $methods = ['get', 'post', 'patch', 'put', 'delete'];

    /**
     * Initialize the client
     *
     * @param string  $host          the base url (e.g. https://api.sendgrid.com)
     * @param array   $headers       global request headers
     * @param string  $version       api version (configurable) - this is specific to the SendGrid API
     * @param array   $path          holds the segments of the url path
     * @param array   $curlOptions   extra options to set during curl initialization
     * @param bool    $retryOnLimit  set default retry on limit flag
     */
    public function __construct($host, $headers = null, $version = null, $path = null, $curlOptions = null, $retryOnLimit = false) {
        $this->host = $host;
        $this->headers = $headers ?: [];
        $this->version = $version;
        $this->path = $path ?: [];
        $this->curlOptions = $curlOptions ?: [];
        $this->retryOnLimit = $retryOnLimit;
        $this->isConcurrentRequest = false;
        $this->savedRequests = [];
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @return string|null
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return array
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getCurlOptions() {
        return $this->curlOptions;
    }

    /**
     * Set extra options to set during curl initialization
     *
     * @param array $options
     *
     * @return CVendor_SendGrid_Client
     */
    public function setCurlOptions(array $options) {
        $this->curlOptions = $options;

        return $this;
    }

    /**
     * Set default retry on limit flag
     *
     * @param bool $retry
     *
     * @return CVendor_SendGrid_Client
     */
    public function setRetryOnLimit($retry) {
        $this->retryOnLimit = $retry;

        return $this;
    }

    /**
     * Set concurrent request flag
     *
     * @param bool $isConcurrent
     *
     * @return CVendor_SendGrid_Client
     */
    public function setIsConcurrentRequest($isConcurrent) {
        $this->isConcurrentRequest = $isConcurrent;

        return $this;
    }

    /**
     * Build the final URL to be passed
     *
     * @param array $queryParams an array of all the query parameters
     *
     * @return string
     */
    private function buildUrl($queryParams = null) {
        $path = '/' . implode('/', $this->path);
        if (isset($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        return sprintf('%s%s%s', $this->host, $this->version ?: '', $path);
    }

    /**
     * Creates curl options for a request
     * this function does not mutate any private variables
     *
     * @param string $method
     * @param array $body
     * @param array $headers
     *
     * @return array
     */
    private function createCurlOptions($method, $body = null, $headers = null) {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FAILONERROR => false
                ] + $this->curlOptions;

        if (isset($headers)) {
            $headers = array_merge($this->headers, $headers);
        } else {
            $headers = $this->headers;
        }

        if (isset($body)) {
            $encodedBody = json_encode($body);
            $options[CURLOPT_POSTFIELDS] = $encodedBody;
            $headers = array_merge($headers, ['Content-Type: application/json']);
        }
        $options[CURLOPT_HTTPHEADER] = $headers;

        return $options;
    }

    /**
     * @param array $requestData
     *      e.g. ['method' => 'POST', 'url' => 'www.example.com', 'body' => 'test body', 'headers' => []]
     * @param bool $retryOnLimit
     *
     * @return array
     */
    private function createSavedRequest(array $requestData, $retryOnLimit = false) {
        return array_merge($requestData, ['retryOnLimit' => $retryOnLimit]);
    }

    /**
     * @param array $requests
     *
     * @return array
     */
    private function createCurlMultiHandle(array $requests) {
        $channels = [];
        $multiHandle = curl_multi_init();

        foreach ($requests as $id => $data) {
            $channels[$id] = curl_init($data['url']);
            $curlOpts = $this->createCurlOptions($data['method'], $data['body'], $data['headers']);
            curl_setopt_array($channels[$id], $curlOpts);
            curl_multi_add_handle($multiHandle, $channels[$id]);
        }

        return [$channels, $multiHandle];
    }

    /**
     * Prepare response object
     *
     * @param resource $channel  the curl resource
     * @param string   $content
     *
     * @return CVendor_SendGrid_Response object
     */
    private function parseResponse($channel, $content) {
        $headerSize = curl_getinfo($channel, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);

        $responseBody = substr($content, $headerSize);

        $responseHeaders = substr($content, 0, $headerSize);
        $responseHeaders = explode("\n", $responseHeaders);
        $responseHeaders = array_map('trim', $responseHeaders);

        return new CVendor_SendGrid_Mail_Response($statusCode, $responseBody, $responseHeaders);
    }

    /**
     * Retry request
     *
     * @param array  $responseHeaders headers from rate limited response
     * @param string $method          the HTTP verb
     * @param string $url             the final url to call
     * @param array  $body            request body
     * @param array  $headers         original headers
     *
     * @return CVendor_SendGrid_Response response object
     */
    private function retryRequest(array $responseHeaders, $method, $url, $body, $headers) {
        $sleepDurations = $responseHeaders['X-Ratelimit-Reset'] - time();
        sleep($sleepDurations > 0 ? $sleepDurations : 0);
        return $this->makeRequest($method, $url, $body, $headers, false);
    }

    /**
     * Make the API call and return the response.
     * This is separated into it's own function, so we can mock it easily for testing.
     *
     * @param string $method       the HTTP verb
     * @param string $url          the final url to call
     * @param array  $body         request body
     * @param array  $headers      any additional request headers
     * @param bool   $retryOnLimit should retry if rate limit is reach?
     *
     * @return CVendor_SendGrid_Response object
     * @throws CVendor_SendGrid_Exception_InvalidRequest
     */
    public function makeRequest($method, $url, $body = null, $headers = null, $retryOnLimit = false) {
        $channel = curl_init($url);

        $options = $this->createCurlOptions($method, $body, $headers);

        curl_setopt_array($channel, $options);
        $content = curl_exec($channel);

        if ($content === false) {
            throw new CVendor_SendGrid_Exception_InvalidRequest(curl_error($channel), curl_errno($channel));
        }

        $response = $this->parseResponse($channel, $content);

        if ($response->statusCode() === self::TOO_MANY_REQUESTS_HTTP_CODE && $retryOnLimit) {
            $responseHeaders = $response->headers(true);
            return $this->retryRequest($responseHeaders, $method, $url, $body, $headers);
        }

        curl_close($channel);

        return $response;
    }

    /**
     * Send all saved requests at once
     *
     * @param array $requests
     *
     * @return CVendor_SendGrid_Response[]
     */
    public function makeAllRequests(array $requests = []) {
        if (empty($requests)) {
            $requests = $this->savedRequests;
        }
        list($channels, $multiHandle) = $this->createCurlMultiHandle($requests);

        // running all requests
        $isRunning = null;
        do {
            curl_multi_exec($multiHandle, $isRunning);
        } while ($isRunning);

        // get response and close all handles
        $retryRequests = [];
        $responses = [];
        $sleepDurations = 0;
        foreach ($channels as $id => $channel) {

            $content = curl_multi_getcontent($channel);
            $response = $this->parseResponse($channel, $content);

            if ($response->statusCode() === self::TOO_MANY_REQUESTS_HTTP_CODE && $requests[$id]['retryOnLimit']) {
                $headers = $response->headers(true);
                $sleepDurations = max($sleepDurations, $headers['X-Ratelimit-Reset'] - time());
                $requestData = [
                    'method' => $requests[$id]['method'],
                    'url' => $requests[$id]['url'],
                    'body' => $requests[$id]['body'],
                    'headers' => $headers,
                ];
                $retryRequests[] = $this->createSavedRequest($requestData, false);
            } else {
                $responses[] = $response;
            }

            curl_multi_remove_handle($multiHandle, $channel);
        }
        curl_multi_close($multiHandle);

        // retry requests
        if (!empty($retryRequests)) {
            sleep($sleepDurations > 0 ? $sleepDurations : 0);
            $responses = array_merge($responses, $this->makeAllRequests($retryRequests));
        }
        return $responses;
    }

    /**
     * Add variable values to the url. (e.g. /your/api/{variable_value}/call)
     * Another example: if you have a PHP reserved word, such as and, in your url, you must use this method.
     *
     * @param string $name name of the url segment
     *
     * @return CVendor_SendGrid_Client object
     */
    public function _($name = null) {
        if (isset($name)) {
            $this->path[] = $name;
        }
        $client = new static($this->host, $this->headers, $this->version, $this->path);
        $client->setCurlOptions($this->curlOptions);
        $client->setRetryOnLimit($this->retryOnLimit);
        $this->path = [];

        return $client;
    }

    /**
     * Dynamically add method calls to the url, then call a method.
     * (e.g. client.name.name.method())
     *
     * @param string $name name of the dynamic method call or HTTP verb
     * @param array  $args parameters passed with the method call
     *
     * @return CVendor_SendGrid_Client|CVendor_SendGrid_Response|CVendor_SendGrid_Response[]|null object
     */
    public function __call($name, $args) {
        $name = strtolower($name);

        if ($name === 'version') {
            $this->version = $args[0];
            return $this->_();
        }

        // send all saved requests
        if (($name === 'send') && $this->isConcurrentRequest) {
            return $this->makeAllRequests();
        }

        if (in_array($name, $this->methods, true)) {
            $body = isset($args[0]) ? $args[0] : null;
            $queryParams = isset($args[1]) ? $args[1] : null;
            $url = $this->buildUrl($queryParams);
            $headers = isset($args[2]) ? $args[2] : null;
            $retryOnLimit = isset($args[3]) ? $args[3] : $this->retryOnLimit;

            if ($this->isConcurrentRequest) {
                // save request to be sent later
                $requestData = ['method' => $name, 'url' => $url, 'body' => $body, 'headers' => $headers];
                $this->savedRequests[] = $this->createSavedRequest($requestData, $retryOnLimit);
                return null;
            }

            return $this->makeRequest($name, $url, $body, $headers, $retryOnLimit);
        }

        return $this->_($name);
    }

}
