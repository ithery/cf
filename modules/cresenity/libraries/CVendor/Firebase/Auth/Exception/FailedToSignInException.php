<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Auth_Exception_FailedToSignInException extends RuntimeException implements CVendor_Firebase_ExceptionInterface {
    /**
     * Undocumented variable.
     *
     * @var null|CVendor_Firebase_Auth_SignInInterface
     */
    private $action = null;

    /**
     * Undocumented variable.
     *
     * @var null|ResponseInterface
     */
    private $response = null;

    /**
     * @param CVendor_Firebase_Auth_SignInInterface $action
     * @param ResponseInterface                     $response
     *
     * @return self
     */
    public static function withActionAndResponse(CVendor_Firebase_Auth_SignInInterface $action, ResponseInterface $response) {
        $fallbackMessage = 'Failed to sign in';

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

    public static function fromPrevious(Throwable $e): self {
        return new self('Sign in failed: ' . $e->getMessage(), $e->getCode(), $e);
    }

    /**
     * @return null|CVendor_Firebase_Auth_SignInInterface
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
