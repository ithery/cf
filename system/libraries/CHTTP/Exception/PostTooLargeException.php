<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

class CHTTP_Exception_PostTooLargeException extends HttpException {
    /**
     * Create a new "post too large" exception instance.
     *
     * @param null|string     $message
     * @param null|\Throwable $previous
     * @param array           $headers
     * @param int             $code
     *
     * @return void
     */
    public function __construct($message = null, $previous = null, array $headers = [], $code = 0) {
        parent::__construct(413, $message, $previous, $headers, $code);
    }
}
