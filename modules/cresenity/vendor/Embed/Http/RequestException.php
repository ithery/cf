<?php

//declare(strict_types=1);

namespace Embed\Http;

use Exception;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;

final class RequestException extends Exception implements RequestExceptionInterface {

    private $request;

    public function __construct($message, $code, RequestInterface $request) {
        $this->request = $request;
    }

    public function getRequest() {
        return $this->request;
    }

}
