<?php

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * @internal
 */
class CVendor_Firebase_Messaging_ApiClient implements ClientInterface {
    use CVendor_Firebase_Trait_WrappedGuzzleClientTrait;

    /**
     * @var CVendor_Firebase_Messaging_ApiExceptionConverter
     */
    private $errorHandler;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @internal
     */
    public function __construct(ClientInterface $client) {
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
    public function getClient() {
        return $this->client;
    }

    /**
     * @deprecated 4.29.0
     */
    public function sendMessage(CVendor_Firebase_Messaging_MessageInterface $message) {
        return $this->send(new CVendor_Firebase_Messaging_Request_SendMessageRequest($this->projectId, $message));
    }

    /**
     * @deprecated 4.29.0
     */
    public function sendMessageAsync(CVendor_Firebase_Messaging_MessageInterface $message) {
        return $this->sendAsync(new CVendor_Firebase_Messaging_Request_SendMessageRequest($this->projectId, $message));
    }

    /**
     * @deprecated 4.29.0
     */
    public function validateMessage(CVendor_Firebase_Messaging_MessageInterface $message) {
        return $this->send(new CVendor_Firebase_Messaging_Request_ValidateMessageRequest($this->projectId, $message));
    }

    /**
     * @deprecated 4.29.0
     */
    public function validateMessageAsync(CVendor_Firebase_Messaging_MessageInterface $message) {
        return $this->sendAsync(new CVendor_Firebase_Messaging_Request_ValidateMessageRequest($this->projectId, $message));
    }

    /**
     * @throws MessagingException
     * @throws CVendor_Firebase_Exception
     */
    public function send(RequestInterface $request, array $options = []) {
        try {
            return $this->client->send($request);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        } catch (Exception $e) {
            throw $this->errorHandler->convertException($e);
        }
    }

    public function sendAsync(RequestInterface $request, array $options = []) {
        return $this->client->sendAsync($request, $options)
            ->then(null, function (Throwable $e) {
                throw $this->errorHandler->convertException($e);
            });
    }
}
