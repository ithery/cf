<?php

namespace DigitalOceanV2\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use DigitalOceanV2\Exception\HttpException;

/**
 * @author Marcos Sigueros <alrik11es@gmail.com>
 * @author Chris Fidao <fideloper@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
class GuzzleHttpAdapter implements AdapterInterface {
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Response|ResponseInterface
     */
    protected $response;

    /**
     * @param string               $token
     * @param null|ClientInterface $client
     */
    public function __construct($token, ClientInterface $client = null) {
        $this->client = $client ?: new Client(['headers' => ['Authorization' => sprintf('Bearer %s', $token)]]);
    }

    /**
     * @inheritdoc
     */
    public function get($url) {
        try {
            $this->response = $this->client->get($url);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function delete($url) {
        try {
            $this->response = $this->client->delete($url);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function put($url, $content = '') {
        $options = [];

        $options[is_array($content) ? 'json' : 'body'] = $content;

        try {
            $this->response = $this->client->put($url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function post($url, $content = '') {
        $options = [];

        $options[is_array($content) ? 'json' : 'body'] = $content;

        try {
            $this->response = $this->client->post($url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function getLatestResponseHeaders() {
        if (null === $this->response) {
            return;
        }

        return [
            'reset' => (int) (string) $this->response->getHeader('RateLimit-Reset'),
            'remaining' => (int) (string) $this->response->getHeader('RateLimit-Remaining'),
            'limit' => (int) (string) $this->response->getHeader('RateLimit-Limit'),
        ];
    }

    /**
     * @throws HttpException
     */
    protected function handleError() {
        $body = (string) $this->response->getBody();
        $code = (int) $this->response->getStatusCode();

        $content = json_decode($body);

        throw new HttpException(isset($content->message) ? $content->message : 'Request not processed.', $code);
    }
}
