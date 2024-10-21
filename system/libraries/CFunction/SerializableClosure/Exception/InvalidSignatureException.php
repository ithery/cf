<?php

class CFunction_SerializableClosure_Exception_InvalidSignatureException extends Exception {
    /**
     * Create a new exception instance.
     *
     * @param string $message
     *
     * @return void
     */
    public function __construct($message = 'Your serialized closure might have been modified or it\'s unsafe to be unserialized.') {
        parent::__construct($message);
    }
}
