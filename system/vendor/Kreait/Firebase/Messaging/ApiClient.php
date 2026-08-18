<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use Iterator;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
class ApiClient
{
    private ClientInterface $client;
    private string $projectId;
    private RequestFactory $requestFactory;

    public function __construct(
        ClientInterface $client,
        string $projectId,
        RequestFactory $requestFactory
    ) {
        $this->client = $client;
        $this->projectId = $projectId;
        $this->requestFactory = $requestFactory;
    }

    public function createSendRequestForMessage(Message $message, bool $validateOnly): RequestInterface
    {
        return $this->requestFactory->createRequest($message, $this->projectId, $validateOnly);
    }

    /**
     * @param list<RequestInterface>|Iterator<RequestInterface> $requests
     * @param array<string, mixed> $config
     */
    public function pool($requests, array $config): PromiseInterface
    {
        $pool = new Pool($this->client, $requests, $config);

        return $pool->promise();
    }
}
