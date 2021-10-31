<?php

use Psr\Http\Message\RequestInterface;

/**
 * @see https://firebase.google.com/docs/auth/server/
 */
interface CVendor_Firebase_Http_AuthInterface {
    /**
     * Returns an authenticated request from the given request.
     */
    public function authenticateRequest(RequestInterface $request);
}
