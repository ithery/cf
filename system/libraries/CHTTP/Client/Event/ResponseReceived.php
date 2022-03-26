<?php

class CHTTP_Client_Event_ResponseReceived {
    /**
     * The request instance.
     *
     * @var \CHTTP_Client_Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \CHTTP_Client_Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param \CHTTP_Client_Request  $request
     * @param \CHTTP_Client_Response $response
     *
     * @return void
     */
    public function __construct(CHTTP_Client_Request $request, CHTTP_Client_Response $response) {
        $this->request = $request;
        $this->response = $response;
    }
}
