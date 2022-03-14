<?php

use Lcobucci\JWT\Token;

final class CVendor_Firebase_Auth_Exception_RevokedIdTokenException extends RuntimeException implements CVendor_Firebase_Auth_ExceptionInterface {
    private Token $token;

    public function __construct(Token $token, $message = '', $code = 0, $previous = null) {
        $message = $message ?: 'The Firebase ID token has been revoked.';

        parent::__construct($message, $code, $previous);

        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken() {
        return $this->token;
    }
}
