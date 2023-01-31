<?php

//declare(strict_types=1);

namespace Embed\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Class to fetch html pages.
 */
final class CurlClient implements ClientInterface {
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var array
     */
    private $settings = [];

    public function __construct(ResponseFactoryInterface $responseFactory = null) {
        $this->responseFactory = $responseFactory ?: FactoryDiscovery::getResponseFactory();
    }

    public function setSettings(array $settings) {
        $this->settings = $settings + $this->settings;
    }

    public function sendRequest(RequestInterface $request) {
        $responses = CurlDispatcher::fetch($this->settings, $this->responseFactory, $request);

        return $responses[0];
    }

    public function sendRequests(RequestInterface ...$request) {
        return CurlDispatcher::fetch($this->settings, $this->responseFactory, ...$request);
    }
}
