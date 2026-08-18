<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Qontak_Exception_ClientSendingException extends Exception {
    public static function make(ResponseInterface $response): self {
        $reason = (string) $response->getBody();

        $code = $response->getStatusCode();

        return new self($reason, $code);
    }
}
