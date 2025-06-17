<?php

use Monolog\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use Monolog\Handler\RotatingFileHandler;
use GuzzleHttp\Exception\RequestException;
use Google\AdsApi\AdManager\v201902\RequestError;

class CVendor_Kataai_Client {
    protected $baseUrl;

    protected $loginData;

    protected $username = null;

    protected $password = null;

    /**
     * @var null|CCache_Repository
     */
    protected $cache;

    protected $client;

    public function __construct(array $options = []) {
        $this->baseUrl = carr::get($options, 'baseUrl', CVendor_Kataai::getBaseUrl());
        $this->cache = carr::get($options, 'cache', c::cache()->store());
        $this->username = carr::get($options, 'username');
        $this->password = carr::get($options, 'password');
        $guzzleOptions = [];

        $client = new Client($guzzleOptions);

        $this->client = $client;

        if ($this->loginData == null) {
            $this->loginData = $this->cache->get($this->getLoginDataCacheKey());
        }

        if ($this->loginData == null) {
            $this->login();
        }
    }

    /**
     * Retrieves the login data.
     *
     * @return array the login data stored in the cache or null if not available
     */
    public function getLoginData() {
        return $this->loginData;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function getLoginDataCacheKey() {
        return CF::appCode() . '.vendor.kataai.loginData.' . $this->username;
    }

    public function login() {
        $loginUrl = $this->baseUrl . '/v1/users/login';
        $response = null;
        $params = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        try {
            $response = $this->client->post($loginUrl, [
                'json' => $params
            ]);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        $body = (string) $response->getBody();
        $bodyData = json_decode($body, true);

        if (isset($bodyData['access_token'])) {
            $this->loginData = $bodyData;
            $this->cache->put($this->getLoginDataCacheKey(), $this->loginData);
        }
    }

    public function logout() {
        $logoutUrl = $this->baseUrl . '/v1/users/logout';

        return $this->post($logoutUrl);
    }

    protected function getDefaultOptions() {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . carr::get($this->loginData, 'access_token'),
                'Content-Type' => 'application/json',
            ]
        ];

        return $options;
    }

    public function get($url, $query = null, $headers = null) {
        $response = null;

        try {
            $options = $this->getDefaultOptions();

            if ($query != null && is_array($query)) {
                $options['query'] = $query;
            }

            $response = $this->client->get($url, $options);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        return (string) $response->getBody();
    }

    public function delete($url, $content = '', $headers = null) {
        $response = null;

        try {
            $options = $this->getDefaultOptions();
            $options[is_array($content) ? 'json' : 'body'] = $content;
            $response = $this->client->delete($url, $options);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        return (string) $response->getBody();
    }

    public function postMultiPart($url, $content) {
        $response = null;

        try {
            $options = $this->getDefaultOptions();
            $options['multipart'] = $content;
            if (isset($options['headers']['Content-Type'])) {
                //we wil unset this
                unset($options['headers']['Content-Type']);
            }

            $response = $this->client->request('POST', $url, $options);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        return (string) $response->getBody();
    }

    public function put($url, $content = '', $headers = null) {
        $response = null;
        $options = $this->getDefaultOptions();

        $options[is_array($content) ? 'json' : 'body'] = $content;

        try {
            $response = $this->client->put($url, $options);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        return (string) $response->getBody();
    }

    /**
     * Sends a POST request to the specified URL with the given content and headers.
     *
     * @param string     $url     the URL to which the request is sent
     * @param mixed      $content The content to be sent in the request body. Can be an array or a string.
     * @param null|array $headers optional headers to include in the request
     *
     * @throws RequestException if the request fails
     *
     * @return string the response body as a string
     */
    public function post($url, $content = '', $headers = null) {
        $response = null;
        $options = $this->getDefaultOptions();
        $options[is_array($content) ? 'json' : 'body'] = $content;

        try {
            $response = $this->client->post($url, $options);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        return (string) $response->getBody();
    }

    /**
     * @param Exception $e
     *
     * @throws CVendor_Wago_Exception_HttpException
     * @throws CVendor_Wago_Exception_ApiException
     */
    protected function handleError($e) {
        $response = null;
        if ($e instanceof RequestException) {
            $response = $e->getResponse();
        }
        if ($response == null) {
            throw $e;
        }
        $body = (string) $response->getBody();
        $code = (int) $response->getStatusCode();
        if ($code != 200) {
            if ($code == 401) {
                $content = json_decode($body, true);
                $errMessage = carr::get($content, 'message');
            }

            throw new CVendor_Kataai_Exception_HttpException(isset($body) ? $body : 'Request not processed.', $code);
        }
        $content = json_decode($body, true);
        $message = carr::get($content, 'message');

        throw new CVendor_Kataai_Exception_ApiException($message ? $message : 'Request not processed.', $code);
    }
}
