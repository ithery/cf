<?php

use Psr\Http\Message\RequestInterface;

final class CVendor_Firebase_Messaging_Request_SendMessageToTokensRequest implements CVendor_Firebase_Http_HasSubRequestsInterface, RequestInterface {
    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    const MAX_AMOUNT_OF_TOKENS = 500;

    public function __construct($projectId, CVendor_Firebase_Messaging_MessageInterface $message, CVendor_Firebase_Messaging_RegistrationTokens $registrationTokens, $validateOnly = false) {
        if ($registrationTokens->count() > self::MAX_AMOUNT_OF_TOKENS) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('A multicast message can be sent to a maximum amount of ' . self::MAX_AMOUNT_OF_TOKENS . ' tokens.');
        }

        $messageData = $message->jsonSerialize();
        unset($messageData['topic'], $messageData['condition']);

        $messages = [];

        foreach ($registrationTokens as $token) {
            $messageData['token'] = $token->value();

            $messages[] = new CVendor_Firebase_Messaging_RawMessageFromArray($messageData);
        }

        $this->wrappedRequest = new CVendor_Firebase_Messaging_Request_SendMessagesRequest($projectId, new CVendor_Firebase_Messaging_Messages(...$messages), $validateOnly);
    }
}
