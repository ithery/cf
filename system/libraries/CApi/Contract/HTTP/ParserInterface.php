<?php

interface CApi_Contract_HTTP_ParserInterface {
    /**
     * Parse an incoming request.
     *
     * @param \CHTTP_Request $request
     *
     * @return mixed
     */
    public function parse(CHTTP_Request $request);
}
