<?php

interface CApi_Contract_HTTP_RequestInterface {
    /**
     * Create a new Dingo request instance from an Illuminate request instance.
     *
     * @param \CHTTP_Request $old
     *
     * @return \CApi_HTTP_Request
     */
    public function createFromBaseHttp(CHTTP_Request $old);
}
