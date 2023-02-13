<?php

class CHTTP_ResponseCache_Exception_CouldNotUnserializeException extends Exception {
    /**
     * @param string $serializedResponse
     *
     * @return CHTTP_ResponseCache_Exception_CouldNotUnserializeException
     */
    public static function serializedResponse($serializedResponse) {
        return new self("Could not unserialize serialized response `{$serializedResponse}`");
    }
}
