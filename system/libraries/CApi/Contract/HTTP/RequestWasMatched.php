<?php

use Dingo\Api\Http\Request;
use Illuminate\Contracts\Container\Container;

class CApi_Event_RequestWasMatched {
    /**
     * Request instance.
     *
     * @var \CApi_HTTP_Request
     */
    public $request;

    /**
     * Create a new request was matched event.
     *
     * @param \CApi_HTTP_Request $request
     *
     * @return void
     */
    public function __construct(CApi_HTTP_Request $request) {
        $this->request = $request;
    }
}
