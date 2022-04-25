<?php

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;

abstract class CVendor_OneSignal_AbstractApi {
    /**
     * @var CVendor_OneSignal
     */
    protected $client;

    public function __construct(CVendor_OneSignal $client) {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return RequestInterface
     */
    protected function createRequest($method, $uri) {
        $request = $this->client->getRequestFactory()->createRequest($method, CVendor_OneSignal::API_URL . $uri);
        $request = $request->withHeader('Accept', 'application/json');

        return $request;
    }

    /**
     * @param mixed      $value
     * @param null|mixed $flags
     * @param mixed      $maxDepth
     * @phpstan-param int<1, max> $maxDepth
     *
     * @return StreamInterface
     */
    protected function createStream($value, $flags = null, $maxDepth = 512) {
        $flags = $flags ?? (JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRESERVE_ZERO_FRACTION);

        try {
            $value = json_encode($value, $flags | JSON_THROW_ON_ERROR, $maxDepth);
        } catch (JsonException $e) {
            throw new CVendor_OneSignal_Exception_InvalidArgumentException("Invalid value for json encoding: {$e->getMessage()}.");
        }

        return $this->client->getStreamFactory()->createStream($value);
    }
}
