<?php

class CHTTP_Client_Event_ConnectionFailed {
    /**
     * The request instance.
     *
     * @var \CHTTP_Client_Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param \CHTTP_Client_Request $request
     *
     * @return void
     */
    public function __construct(CHTTP_Client_Request $request) {
        $this->request = $request;
    }
}
