<?php

use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class CHTTP_Exception_ThrottleRequestException extends TooManyRequestsHttpException {
    /**
     * Create a new throttle requests exception instance.
     *
     * @param null|string     $message
     * @param null|\Throwable $previous
     * @param array           $headers
     * @param int             $code
     *
     * @return void
     */
    public function __construct($message = null, $previous = null, array $headers = [], $code = 0) {
        parent::__construct(null, $message, $previous, $code, $headers);
    }
}
