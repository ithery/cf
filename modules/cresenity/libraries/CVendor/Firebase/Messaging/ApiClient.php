<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * @internal
 */
class CVendor_Firebase_Messaging_ApiClient implements ClientInterface
{
    use CVendor_Firebase_Trait_WrappedGuzzleClientTrait;

    /** @var MessagingApiExceptionConverter */
    private $errorHandler;

    /** @var string */
    private $projectId;

    /**
     * @internal
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->errorHandler = new CVendor_Firebase_Messaging_ApiExceptionConverter();

        // Extract the project ID from the client config (this will be refactored later)
        $baseUri = (string) $client->getConfig('base_uri');
        $uriParts = \explode('/', $baseUri);
        $this->projectId = (string) \array_pop($uriParts);
    }

    /**
     * @internal
     *
     * @deprecated 4.29.0
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @deprecated 4.29.0
     */
    public function sendMessage(CVendor_Firebase_Messaging_MessageInterface $message)
    {
        return $this->send(new SendMessage($this->projectId, $message));
    }

    /**
     * @deprecated 4.29.0
     */
    public function sendMessageAsync(CVendor_Firebase_Messaging_MessageInterface $message)
    {
        return $this->sendAsync(new SendMessage($this->projectId, $message));
    }

    /**
     * @deprecated 4.29.0
     */
    public function validateMessage(CVendor_Firebase_Messaging_MessageInterface $message)
    {
        return $this->send(new ValidateMessage($this->projectId, $message));
    }

    /**
     * @deprecated 4.29.0
     */
    public function validateMessageAsync(CVendor_Firebase_Messaging_MessageInterface $message)
    {
        return $this->sendAsync(new ValidateMessage($this->projectId, $message));
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     */
    public function send(RequestInterface $request, array $options = [])
    {
        try {
            return $this->client->send($request);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }

    public function sendAsync(RequestInterface $request, array $options = [])
    {
        return $this->client->sendAsync($request, $options)
            ->then(null, function (Throwable $e) {
                throw $this->errorHandler->convertException($e);
            });
    }
}