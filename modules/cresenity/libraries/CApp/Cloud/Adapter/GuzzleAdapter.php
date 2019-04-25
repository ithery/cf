<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 7:07:06 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

class CApp_Cloud_Adapter_GuzzleAdapter extends CApp_Cloud_AdapterAbstract {

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param string               $token
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
     * @throws HttpException
     */
    protected function handleError($e) {
        if ($this->response == null) {
            throw $e;
        }
        $body = (string) $this->response->getBody();
        $code = (int) $this->response->getStatusCode();
        $content = json_decode($body);
        throw new CApp_Cloud_Exception_HttpException(isset($content->message) ? $content->message : 'Request not processed.', array(), $code);
    }

}
