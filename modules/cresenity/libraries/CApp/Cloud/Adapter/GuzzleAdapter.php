<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2019, 7:07:06 AM
 */
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

class CApp_Cloud_Adapter_GuzzleAdapter extends CApp_Cloud_AdapterAbstract {
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null) {
        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function get($url) {
        try {
            $this->response = $this->client->get($url);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }
        return (string) $this->response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url) {
        try {
            $this->response = $this->client->delete($url);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }
        return (string) $this->response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $content = '') {
        $options = [];
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
     * {@inheritdoc}
     */
    public function post($url, $content = '') {
        $options = [];
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
     * {@inheritdoc}
     */
    public function getLatestResponseHeaders() {
        if (null === $this->response) {
            return null;
        }
        return [
            'reset' => (int) (string) $this->response->getHeader('RateLimit-Reset'),
            'remaining' => (int) (string) $this->response->getHeader('RateLimit-Remaining'),
            'limit' => (int) (string) $this->response->getHeader('RateLimit-Limit'),
        ];
    }

    /**
     * @param mixed $e
     *
     * @throws HttpException
     */
    protected function handleError($e) {
        if ($this->response == null) {
            throw $e;
        }
        $body = (string) $this->response->getBody();
        $code = (int) $this->response->getStatusCode();
        if ($code != 200) {
            throw new CApp_Cloud_Exception_HttpException(isset($body) ? $body : 'Request not processed.', [], $code);
        }
        $content = json_decode($body);

        throw new CApp_Cloud_Exception_ApiException(isset($content->errMessage) ? $content->errMessage : 'Request not processed.', [], $code);
    }
}
