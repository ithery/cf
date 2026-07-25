<?php

class CApp_Api_Exception extends Exception {
    /**
     * @var int
     */
    protected $httpCode = 503;

    /**
     * @return CHTTP_JsonResponse
     */
    public function toJsonResponse() {
        return c::response()->json([
            'errCode' => $this->getCode(),
            'errMessage' => $this->getMessage()
        ], $this->httpCode);
    }
}
