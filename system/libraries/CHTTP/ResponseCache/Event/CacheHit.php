<?php

class CHTTP_ResponseCache_Event_CacheHit {
    public $request;

    public function __construct(
        CHTTP_Request $request
    ) {
        $this->request = $request;
    }
}
