<?php

class CWebhook_Client_Event_InvalidWebhookSignatureEvent {
    /**
     * @var CHTTP_Request
     */
    protected $request;

    public function __construct(
        $request
    ) {
        $this->request = $request;
    }
}
