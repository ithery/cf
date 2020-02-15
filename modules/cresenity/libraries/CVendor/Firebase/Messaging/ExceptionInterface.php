<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
