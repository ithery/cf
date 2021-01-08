<?php

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class CVendor_Firebase_Messaging_AppInstanceApiClient {
    /** @var ClientInterface */
    private $client;

    /** @var CVendor_Firebase_Messaging_ApiExceptionConverter */
    private $errorHandler;

    /**
     * @internal
     */
    public function __construct(ClientInterface $client) {
        $this->client = $client;
        $this->errorHandler = new CVendor_Firebase_Messaging_ApiExceptionConverter();
    }

    /**
     * @param CVendor_Firebase_Messaging_Topic|string                 $topic
     * @param CVendor_Firebase_Messaging_RegistrationToken[]|string[] $tokens
     *
     * @throws FirebaseException
     * @throws MessagingException
     *
     * @see https://developers.google.com/instance-id/reference/server#manage_relationship_maps_for_multiple_app_instances     *
     */
    public function subscribeToTopic($topic, array $tokens) {
        return $this->requestApi('POST', '/iid/v1:batchAdd', [
            'json' => [
                'to' => '/topics/' . $topic,
                'registration_tokens' => $tokens,
            ],
        ]);
    }

    /**
     * @param Topic|string                 $topic
     * @param RegistrationToken[]|string[] $tokens
     *
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function unsubscribeFromTopic($topic, array $tokens) {
        return $this->requestApi('POST', '/iid/v1:batchRemove', [
            'json' => [
                'to' => '/topics/' . $topic,
                'registration_tokens' => $tokens,
            ],
        ]);
    }

    /**
     * @param mixed $registrationToken
     *
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function getAppInstance($registrationToken) {
        return $this->requestApi('GET', '/iid/' . $registrationToken . '?details=true');
    }

    /**
     * @param mixed $method
     * @param mixed $endpoint
     *
     * @throws FirebaseException
     * @throws MessagingException
     */
    private function requestApi($method, $endpoint, array $options = null) {
        try {
            return $this->client->request($method, $endpoint, $options != null ? $options : []);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
