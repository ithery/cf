<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Auth_Exception_FailedToCreateSessionCookieException extends \RuntimeException implements CVendor_Firebase_ExceptionInterface {
    /**
     * @var CVendor_Firebase_Auth_CreateSessionCookie
     */
    private $action;

    /**
     * @var null|ResponseInterface
     */
    private $response;

    /**
     * @param CVendor_Firebase_Auth_CreateSessionCookie $action
     * @param null|ResponseInterface                    $response
     * @param null|string                               $message
     * @param null|int                                  $code
     * @param null|Throwable                            $previous
     */
    public function __construct(CVendor_Firebase_Auth_CreateSessionCookie $action, ResponseInterface $response = null, $message = null, $code = null, $previous = null) {
        $message ??= '';
        $code ??= 0;

        parent::__construct($message, $code, $previous);

        $this->action = $action;
        $this->response = $response;
    }

    public static function withActionAndResponse(CVendor_Firebase_Auth_CreateSessionCookie $action, ResponseInterface $response) {
        $fallbackMessage = 'Failed to create session cookie';

        try {
            $message = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true)['error']['message'] ?? $fallbackMessage;
        } catch (\InvalidArgumentException $e) {
            $message = $fallbackMessage;
        }

        return new self($action, $response, $message);
    }

    /**
     * @return CVendor_Firebase_Auth_CreateSessionCookie
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
