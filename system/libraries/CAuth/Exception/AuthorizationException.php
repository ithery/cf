<?php

class CAuth_Exception_AuthorizationException extends Exception {
    /**
     * The response from the gate.
     *
     * @var CAuth_Access_Response
     */
    protected $response;

    /**
     * Create a new authorization exception instance.
     *
     * @param string|null     $message
     * @param mixed           $code
     * @param \Throwable|null $previous
     *
     * @return void
     */
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct($message ?: 'This action is unauthorized.', 0, $previous);

        $this->code = $code ?: 0;
    }

    /**
     * Get the response from the gate.
     *
     * @return CAuth_Access_Response
     */
    public function response() {
        return $this->response;
    }

    /**
     * Set the response from the gate.
     *
     * @param CAuth_Access_Response $response
     *
     * @return $this
     */
    public function setResponse($response) {
        $this->response = $response;

        return $this;
    }

    /**
     * Create a deny response object from this exception.
     *
     * @return CAuth_Access_Response
     */
    public function toResponse() {
        return CAuth_Access_Response::deny($this->message, $this->code);
    }
}
