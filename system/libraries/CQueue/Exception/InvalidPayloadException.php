<?php

class CQueue_Exception_InvalidPayloadException extends InvalidArgumentException {
    /**
     * Create a new exception instance.
     *
     * @param null|string $message
     *
     * @return void
     */
    public function __construct($message = null) {
        parent::__construct($message ?: json_last_error());
    }
}
