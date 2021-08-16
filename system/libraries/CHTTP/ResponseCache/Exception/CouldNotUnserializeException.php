<?php

class CHTTP_ResponseCache_Exception_CouldNotUnserializeException extends Exception {
    /**
     * @param string $serializedResponse
     *
     * @return static
     */
    public static function serializedResponse($serializedResponse) {
        return new static("Could not unserialize serialized response `{$serializedResponse}`");
    }
}
