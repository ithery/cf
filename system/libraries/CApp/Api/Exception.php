<?php

class CApp_Api_Exception extends Exception {
    protected $httpCode = 503;

    public function toJsonResponse() {
        return c::response()->json([
            'errCode' => $this->getCode(),
            'errMessage' => $this->getMessage()
        ], $this->httpCode);
    }
}
