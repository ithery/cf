<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 7:07:06 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Response;

class CApp_Cloud_Adapter_GuzzleAdapter extends CApp_Cloud_Adapter {

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
            $this->response = $this->client->get($url)->send();
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody(true);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url) {
        try {
            $this->response = $this->client->delete($url)->send();
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody(true);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $content = '') {
        $request = $this->client->put($url);

        if (is_array($content)) {
            $request->setBody(json_encode($content), 'application/json');
        } else {
            $request->setBody($content);
        }

        try {
            $this->response = $request->send();
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody(true);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $content = '') {
        $request = $this->client->post($url);

        if (is_array($content)) {
            $request->setBody(json_encode($content), 'application/json');
        } else {
            $request->setBody($content);
        }

        try {
            $this->response = $request->send();
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError();
        }

        return $this->response->getBody(true);
    }

    /**
     * {@inheritdoc}
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
        $body = (string) $this->response->getBody(true);
        $code = (int) $this->response->getStatusCode();

        $content = json_decode($body);

        throw new CApp_Cloud_Exception_HttpException(isset($content->message) ? $content->message : 'Request not processed.', $code);
    }

}
