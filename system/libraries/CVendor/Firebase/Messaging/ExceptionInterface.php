<?php

use Psr\Http\Message\ResponseInterface;

interface CVendor_Firebase_Messaging_ExceptionInterface extends CVendor_Firebase_ExceptionInterface {
    /**
     * @return string[]
     */
    public function errors();

    /**
     * @deprecated 4.28.0
     *
     * @return ResponseInterface|null
     */
    public function response();
}
