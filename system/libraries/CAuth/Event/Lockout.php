<?php

class CAuth_Event_Lockout {
    /**
     * The throttled request.
     *
     * @var CHTTP_Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param CHTTP_Request $request
     *
     * @return void
     */
    public function __construct(CHTTP_Request $request) {
        $this->request = $request;
    }
}
