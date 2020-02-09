<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
