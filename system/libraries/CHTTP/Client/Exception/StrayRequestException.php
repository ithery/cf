<?php

class CHTTP_Client_Exception_StrayRequestException extends RuntimeException {
    public function __construct(string $uri) {
        parent::__construct('Attempted request to [' . $uri . '] without a matching fake.');
    }
}
