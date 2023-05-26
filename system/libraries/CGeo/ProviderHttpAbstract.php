<?php

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use Http\Discovery\MessageFactoryDiscovery;

abstract class CGeo_ProviderHttpAbstract extends CGeo_ProviderAbstract {
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @param HttpClient          $client
     * @param null|MessageFactory $factory
     */
    public function __construct(HttpClient $client, MessageFactory $factory = null) {
        $this->client = $client;
        $this->messageFactory = $factory ?: MessageFactoryDiscovery::find();
    }

    /**
     * Get URL and return contents. If content is empty, an exception will be thrown.
     *
     * @param string $url
     *
     * @throws CGeo_Exception_InvalidServerResponse
     *
     * @return string
     */
    protected function getUrlContents(string $url): string {
        $request = $this->getRequest($url);

        return $this->getParsedResponse($request);
    }

    /**
     * @param string $url
     *
     * @return RequestInterface
     */
    protected function getRequest(string $url) {
        return $this->getMessageFactory()->createRequest('GET', $url);
    }

    /**
     * Send request and return contents. If content is empty, an exception will be thrown.
     *
     * @param RequestInterface $request
     *
     * @throws CGeo_Exception_InvalidServerResponse
     *
     * @return string
     */
    protected function getParsedResponse(RequestInterface $request): string {
        $response = $this->getHttpClient()->sendRequest($request);

        $statusCode = $response->getStatusCode();
        if (401 === $statusCode || 403 === $statusCode) {
            throw new CGeo_Exception_InvalidCredentials();
        } elseif (429 === $statusCode) {
            throw new CGeo_Exception_QuotaExceeded();
        } elseif ($statusCode >= 300) {
            throw CGeo_Exception_InvalidServerResponse::create((string) $request->getUri(), $statusCode);
        }

        $body = (string) $response->getBody();
        if ('' === $body) {
            throw CGeo_Exception_InvalidServerResponse::emptyResponse((string) $request->getUri());
        }

        return $body;
    }

    /**
     * Returns the HTTP adapter.
     *
     * @return HttpClient
     */
    protected function getHttpClient() {
        return $this->client;
    }

    /**
     * @return MessageFactory
     */
    protected function getMessageFactory() {
        return $this->messageFactory;
    }
}
