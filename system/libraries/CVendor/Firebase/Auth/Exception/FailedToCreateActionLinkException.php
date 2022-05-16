<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException extends RuntimeException implements CVendor_Firebase_ExceptionInterface {
    /**
     * @var null|CVendor_Firebase_Auth_CreateActionLink
     */
    private $action = null;

    /**
     * @var null|ResponseInterface
     */
    private $response = null;

    public static function withActionAndResponse(CVendor_Firebase_Auth_CreateActionLink $action, ResponseInterface $response): self {
        $fallbackMessage = 'Failed to create action link';

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
     * @return null|CVendor_Firebase_Auth_CreateActionLink
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
