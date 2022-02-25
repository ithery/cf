<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Messaging_Exception_ServerUnavailableException extends RuntimeException implements CVendor_Firebase_Messaging_ExceptionInterface {
    use CVendor_Firebase_Trait_ExceptionHasErrorsTrait;

    /**
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
     * @param DateTimeImmutable $retryAfter
     *
     * @return self
     */
    public function withRetryAfter(DateTimeImmutable $retryAfter) {
        $new = new self($this->getMessage(), $this->getCode(), $this->getPrevious());
        $new->errors = $this->errors;
        $new->retryAfter = $retryAfter;

        return $new;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function retryAfter() {
        return $this->retryAfter;
    }
}
