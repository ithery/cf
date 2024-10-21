<?php

class CFunction_SerializableClosure_Exception_PhpVersionNotSupportedException extends Exception {
    /**
     * Create a new exception instance.
     *
     * @param string $message
     *
     * @return void
     */
    public function __construct($message = 'PHP 7.3 is not supported.') {
        parent::__construct($message);
    }
}
