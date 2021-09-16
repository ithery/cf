
<?php

use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use function GuzzleHttp\Psr7\uri_for;
use Psr\Http\Message\RequestInterface;

/**
 * @deprecated 5.14.0 use {@see SendMessage} instead
 */
final class CVendor_Firebase_Messaging_Request_ValidateMessageRequest implements CVendor_Firebase_Messaging_Request_MessageRequestInterface, RequestInterface {
    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    /** @var CVendor_Firebase_Messaging_MessageInterface */
    private $message;

    public function __construct(string $projectId, CVendor_Firebase_Messaging_MessageInterface $message) {
        $uri = uri_for('https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send');
        $body = stream_for(\json_encode(['message' => $message, 'validate_only' => true]));
        $headers = [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => $body->getSize(),
        ];

        $this->wrappedRequest = new Request('POST', $uri, $headers, $body);
        $this->message = $message;
    }

    public function message() {
        return $this->message;
    }
}
