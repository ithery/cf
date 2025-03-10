<?php

class CAI_Exception_ClientException extends Exception {
    /**
     * Create a new HuggingFaceClientException instance.
     *
     * @param string          $message
     * @param int             $code
     * @param null|\Throwable $previous
     */
    public function __construct($message = 'An error occurred with API', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the custom error message for this exception.
     *
     * @return string
     */
    public function errorMessage() {
        return 'Client Error: ' . $this->getMessage();
    }
}
