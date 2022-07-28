<?php

class CHTTP_Exception_RedirectHttpException extends CHTTP_Exception_HttpException {
    protected $uri;

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
        parent::__construct(307, $message, $previous, $headers, $code);
    }

    public function getUri() {
        return $this->uri;
    }

    public function setUri($uri) {
        $this->uri = $uri;

        return $this;
    }
}
