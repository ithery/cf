<?php

final class CVendor_Firebase_Messaging_Exception_QuotaExceededException extends RuntimeException implements CVendor_Firebase_Messaging_ExceptionInterface {
    use CVendor_Firebase_Trait_ExceptionHasErrorsTrait;
    /**
     * Undocumented variable.
     *
     * @var null|DateTimeImmutable
     */
    private $retryAfter = null;

    /**
     * @param string[] $errors
     *
     * @internal
     *
     * @return self
     */
    public function withErrors(array $errors) {
        $new = new self($this->getMessage(), $this->getCode(), $this->getPrevious());
        $new->errors = $errors;
        $new->retryAfter = $this->retryAfter;

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
