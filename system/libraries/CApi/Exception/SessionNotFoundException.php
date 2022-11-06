<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

class CApi_Exception_SessionNotFoundException extends HttpException {
    /**
     * Create a new unknown version exception instance.
     *
     * @param string     $message
     * @param \Exception $previous
     * @param int        $code
     *
     * @return void
     */
    public function __construct($message = null, Exception $previous = null, $code = 0) {
        parent::__construct(403, $message ?: 'Session not found.', $previous, [], $code);
    }
}
