<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException extends RuntimeException implements CVendor_Firebase_ExceptionInterface {
    /**
     * @var null|CVendor_Firebase_Auth_SendActionLink
     */
    private $action = null;

    /**
     * @var null|ResponseInterface
     */
    private $response = null;

    public static function withActionAndResponse(CVendor_Firebase_Auth_SendActionLink $action, ResponseInterface $response): self {
        $fallbackMessage = 'Failed to send action link';

        try {
            $message = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true)['error']['message'] ?? $fallbackMessage;
        } catch (InvalidArgumentException $e) {
            $message = $fallbackMessage;
        }

        $error = new self($message);
        $error->action = $action;
        $error->response = $response;

        return $error;
    }

    /**
     * @return null|CVendor_Firebase_Auth_SendActionLink
     */
    public function action() {
        return $this->action;
    }

    /**
     * @return null|ResponseInterface
     */
    public function response() {
        return $this->response;
    }
}
