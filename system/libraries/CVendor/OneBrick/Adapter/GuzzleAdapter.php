<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2019, 7:07:06 AM
 */
use Monolog\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use Monolog\Handler\RotatingFileHandler;
use GuzzleHttp\Exception\RequestException;

class CVendor_OneBrick_Adapter_GuzzleAdapter implements CVendor_OneBrick_Contract_AdapterInterface {
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

    protected $clientId;

    protected $baseUri;

    protected $clientSecret;

    protected $cache;

    protected $type;

    /**
     * @param mixed                  $options
     * @param null|CCache_Repository $cacheAdapter
     */
    public function __construct(array $options = [], $cacheAdapter = null) {
        $this->cache = $cacheAdapter;
        if ($this->cache == null) {
            $this->cache = c::cache()->store();
        }
        $this->isLog = carr::get($options, 'logging', false);
        $this->clientId = carr::get($options, 'client_id');
        $this->clientSecret = carr::get($options, 'client_secret');
        $this->baseUri = carr::get($options, 'base_uri');

        $this->type = carr::get($options, 'type');
        // $this->timeout = carr::get($options, 'timeout', 2);
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
        //$options['base_uri'] = $this->baseUri;
        $client = new Client($options);

        $this->client = $client;
    }

    public function getAccessToken() {
        $cacheKey = 'onebrick:accessToken:' . $this->type;
        $accessToken = null;
        if ($this->type == CVendor_OneBrick::TYPE_DATA) {
            $accessToken = $this->cache->get($cacheKey);
        }
        //$accessToken = null;
        $url = $this->baseUri . '/auth/token';

        // cdbg::dd($url);
        if ($accessToken == null) {
            try {
                $this->response = $this->client->request('GET', $url, [
                    'auth' => [$this->clientId, $this->clientSecret]
                ]);
            } catch (RequestException $e) {
                $this->response = $e->getResponse();
                $this->handleError($e);
            }
            $response = (string) $this->response->getBody();
            $responseData = json_decode($response, true);
            if ($this->type == CVendor_OneBrick::TYPE_DATA) {
                $accessToken = carr::get($responseData, 'data.access_token');
                $expiresInSeconds = 60 * 60;
            }
            if ($this->type == CVendor_OneBrick::TYPE_PAYMENT) {
                $accessToken = carr::get($responseData, 'data.accessToken');
                $expiresAt = carr::get($responseData, 'data.expiresAt');
                $expiresInSeconds = 5 * 60;
                if ($expiresAt) {
                    $expiresInSeconds = CCarbon::parse($expiresAt);
                }
            }
            if ($this->type == CVendor_OneBrick::TYPE_DATA) {
                $this->cache->put($cacheKey, $accessToken, $expiresInSeconds);
            }
        }

        return $accessToken;
    }

    protected function getDefaultOptions() {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ];

        if ($this->type == CVendor_OneBrick::TYPE_DATA) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->getAccessToken();
        }
        if ($this->type == CVendor_OneBrick::TYPE_PAYMENT) {
            $options['headers']['PublicAccessToken'] = 'Bearer ' . $this->getAccessToken();
        }

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
     * @throws CVendor_OneBrick_Exception_HttpException
     * @throws CVendor_OneBrick_Exception_ApiException
     */
    protected function handleError($e) {
        if ($this->response == null) {
            throw $e;
        }
        $body = (string) $this->response->getBody();
        $code = (int) $this->response->getStatusCode();
        if ($code != 200) {
            throw new CVendor_OneBrick_Exception_HttpException(isset($body) ? $body : 'Request not processed.', $code);
        }
        $content = json_decode($body);

        throw new CVendor_OneBrick_Exception_ApiException(isset($content->message) ? $content->message : 'Request not processed.', $code);
    }
}
