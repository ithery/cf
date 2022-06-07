<?php

class CApi_OAuth_Exception_InvalidAuthTokenException extends CAuth_Exception_AuthorizationException {
    /**
     * Create a new InvalidAuthTokenException for different auth tokens.
     *
     * @return static
     */
    public static function different() {
        return new static('The provided auth token for the request is different from the session auth token.');
    }
}
