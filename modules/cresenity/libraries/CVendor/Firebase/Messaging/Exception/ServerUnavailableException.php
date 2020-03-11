<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Messaging_Exception_ServerUnavailableException extends RuntimeException implements CVendor_Firebase_Messaging_ExceptionInterface {

    use CVendor_Firebase_Trait_ExceptionHasRequestAndResponseTrait;
    use CVendor_Firebase_Trait_ExceptionHasErrorsTrait;

    /**
     * @internal
     *
     * @param string[] $errors
     *
     * @return static
     */
    public function withErrors(array $errors) {
        $new = new self($this->getMessage(), $this->getCode(), $this->getPrevious());
        $new->errors = $errors;
        $new->response = $this->response;

        return $new;
    }

    /**
     * @internal
     *
     * @deprecated 4.28.0
     *
     * @return static
     */
    public function withResponse(ResponseInterface $response) {
        $new = new self($this->getMessage(), $this->getCode(), $this->getPrevious());
        $new->errors = $this->errors;
        $new->response = $response;

        return $new;
    }

}
