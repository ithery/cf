<?php

class CApi_MethodResponse {
    /**
     * @var CApi_MethodAbstract
     */
    protected $method;

    protected $request;

    public function __construct(CApi_HTTP_Request $request, CApi_MethodAbstract $method) {
        $this->method = $method;
        $this->request = $request;
    }

    public function toResponse($format = 'json') {
        $response = new CApi_HTTP_Response($this->method->result());
        $response->morph($format);

        return $response;
    }
}
