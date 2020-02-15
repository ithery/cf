<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * @internal
 */
class CVendor_Firebase_Messaging_AppInstanceApiClient {

    /** @var ClientInterface */
    private $client;

    /** @var MessagingApiExceptionConverter */
    private $errorHandler;

    /**
     * @internal
     */
    public function __construct(ClientInterface $client) {
        $this->client = $client;
        $this->errorHandler = new CVendor_Firebase_Messaging_ApiExceptionConverter();
    }

    /**
     * @param Topic|string $topic
     * @param RegistrationToken[]|string[] $tokens
     *
     * @throws FirebaseException
     * @throws MessagingException
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
     * @param Topic|string $topic
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
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function getAppInstance($registrationToken) {
        return $this->requestApi('GET', '/iid/' . $registrationToken . '?details=true');
    }

    /**
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
