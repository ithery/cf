<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

trait CVendor_Firebase_Trait_ExceptionHasRequestAndResponseTrait {
    /**
     * @var null|RequestInterface
     */
    protected $request;

    /**
     * @var null|ResponseInterface
     */
    protected $response;

    /**
     * @deprecated 4.28.0
     *
     * @return null|RequestInterface
     */
    public function getRequest() {
        if ($this->request) {
            return $this->request;
        }

        if ($previous = $this->getPreviousIfItIsARequestException()) {
            return $previous->getRequest();
        }

        return null;
    }

    /**
     * @deprecated 4.28.0
     *
     * @return null|RequestInterface
     */
    public function request() {
        return $this->getRequest();
    }

    /**
     * @deprecated 4.28.0
     *
     * @return null|ResponseInterface
     */
    public function getResponse() {
        if ($this->response) {
            return $this->response;
        }

        if ($previous = $this->getPreviousIfItIsARequestException()) {
            return $previous->getResponse();
        }

        return null;
    }

    /**
     * @deprecated 4.28.0
     *
     * @return null|ResponseInterface
     */
    public function response() {
        return $this->getResponse();
    }

    /**
     * @return null|RequestException
     */
    private function getPreviousIfItIsARequestException() {
        if (!($this instanceof Throwable)) {
            return null;
        }

        if (!($previous = $this->getPrevious())) {
            return null;
        }

        if ($previous instanceof RequestException) {
            return $previous;
        }

        /** @var Throwable $previous */
        if (($prePrevious = $previous->getPrevious()) && ($prePrevious instanceof RequestException)) {
            return $prePrevious;
        }

        return null;
    }
}
