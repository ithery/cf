<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

class CRouting_Exception_InvalidSignatureException extends HttpException {
    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(403, 'Invalid signature.');
    }
}
