<?php

interface CApi_Contract_ExceptionHandlerInterface {
    /**
     * Handle an exception.
     *
     * @param \CApi_HTTP_Request    $request
     * @param \Throwable|\Exception $exception
     *
     * @return \CHTTP_Response
     */
    public function handle($request, $exception);
}
