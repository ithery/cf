<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use function GuzzleHttp\Psr7\uri_for;
use Psr\Http\Message\RequestInterface;

final class CVendor_Firebase_Messaging_Request_SendMessageRequest implements RequestInterface {

    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    public function __construct($projectId, CVendor_Firebase_Messaging_MessageInterface $message) {
        $uri = uri_for('https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send');
        $body = stream_for(\json_encode(['message' => $message]));
        $headers = [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => $body->getSize(),
        ];

        $this->wrappedRequest = new Request('POST', $uri, $headers, $body);
    }

}
