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
        $result = new CApi_Result($this->request, $this->method->result());
        $formatted = $result->morph($format);

        return c::response()->json($formatted);
    }
}
