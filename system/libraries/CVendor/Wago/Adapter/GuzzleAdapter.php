<?php

defined('SYSPATH') or die('No direct access allowed.');

use Monolog\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use Monolog\Handler\RotatingFileHandler;
use GuzzleHttp\Exception\RequestException;

/**
 * @deprecated since 1.7 use \Cresenity\Vendor\Wago\Adapter\GuzzleAdapter
 */
class CVendor_Wago_Adapter_GuzzleAdapter implements CVendor_Wago_Contract_AdapterInterface {
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Response
     */
    protected $response;

    protected $token;

    protected $isLog;

    /**
     * @param mixed $options
     */
    public function __construct(array $options = []) {
        $this->isLog = carr::get($options, 'logging', false);
        $this->token = carr::get($options, 'token');
        $options = [];

        if ($this->isLog) {
            $messageFormats = [
                'REQUEST: {method} - {uri} - HTTP/{version} - {req_headers} - {req_body}',
                'RESPONSE: {code} - {res_body}',
            ];

            $stack = HandlerStack::create();

            c::collect($messageFormats)->each(function ($messageFormat) use ($stack) {
                // We'll use unshift instead of push, to add the middleware to the bottom of the stack, not the top
                $stack->unshift(
                    Middleware::log(
                        c::with(new Logger('guzzle-log'))->pushHandler(
                            new RotatingFileHandler(DOCROOT . 'temp/logs/vendor/' . CF::appCode() . '/wago/guzzle-log.log')
                        ),
                        new MessageFormatter($messageFormat)
                    )
                );
            });
            $options['handler'] = $stack;
        }
        $client = new Client($options);

        $this->client = $client;
    }

    protected function getDefaultOptions() {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ];

        return $options;
    }

    /**
     * @inheritdoc
     */
    public function get($url, $query = null, $headers = null) {
        try {
            $options = $this->getDefaultOptions();

            if ($query != null && is_array($query)) {
                $options['query'] = $query;
            }

            $this->response = $this->client->get($url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }

        return (string) $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function delete($url, $content = '', $headers = null) {
        try {
            $options = $this->getDefaultOptions();
            $options[is_array($content) ? 'json' : 'body'] = $content;
            $this->response = $this->client->delete($url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }

        return (string) $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function postMultiPart($url, $content) {
        try {
            $options = $this->getDefaultOptions();
            $options['multipart'] = $content;
            if (isset($options['headers']['Content-Type'])) {
                //we wil unset this
                unset($options['headers']['Content-Type']);
            }

            $this->response = $this->client->request('POST', $url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }

        return (string) $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function put($url, $content = '', $headers = null) {
        $options = $this->getDefaultOptions();

        $options[is_array($content) ? 'json' : 'body'] = $content;

        try {
            $this->response = $this->client->put($url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }

        return (string) $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function post($url, $content = '', $headers = null) {
        $options = $this->getDefaultOptions();
        $options[is_array($content) ? 'json' : 'body'] = $content;

        try {
            $this->response = $this->client->post($url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }

        return (string) $this->response->getBody();
    }

    /**
     * @param mixed $e
     *
     * @throws CVendor_Wago_Exception_HttpException
     * @throws CVendor_Wago_Exception_ApiException
     */
    protected function handleError($e) {
        if ($this->response == null) {
            throw $e;
        }
        $body = (string) $this->response->getBody();
        $code = (int) $this->response->getStatusCode();
        if ($code != 200) {
            if ($code == 401) {
                $content = json_decode($body, true);
                $errMessage = carr::get($content, 'errMessage');
                if ($errMessage == 'Token Not Found or Invalid Token') {
                    throw new CVendor_Wago_Exception_InvalidTokenException($errMessage);
                }
            }

            throw new CVendor_Wago_Exception_HttpException(isset($body) ? $body : 'Request not processed.', $code);
        }
        $content = json_decode($body);

        throw new CVendor_Wago_Exception_ApiException(isset($content->message) ? $content->message : 'Request not processed.', $code);
    }
}
