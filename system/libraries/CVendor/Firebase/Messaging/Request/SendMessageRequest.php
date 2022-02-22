<?php

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

final class CVendor_Firebase_Messaging_Request_SendMessageRequest implements RequestInterface {
    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    public function __construct($projectId, CVendor_Firebase_Messaging_MessageInterface $message, $validateOnly = false) {
        $uri = Utils::uriFor('https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send');
        $body = Utils::streamFor(\json_encode(['message' => $message, 'validate_only' => $validateOnly]));
        $headers = [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => (string) $body->getSize(),
        ];

        $this->wrappedRequest = new Request('POST', $uri, $headers, $body);
    }
}
