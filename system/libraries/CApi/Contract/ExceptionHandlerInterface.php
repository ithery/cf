<?php

interface CApi_Contract_ExceptionHandlerInterface {
    /**
     * Handle an exception.
     *
     * @param \Throwable|\Exception $exception
     *
     * @return \CHTTP_Response
     */
    public function handle($exception);
}
