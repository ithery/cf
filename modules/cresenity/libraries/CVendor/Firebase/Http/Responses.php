<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Http_Responses implements IteratorAggregate {
    /** @var ResponseInterface[] */
    private $responses;

    public function __construct(ResponseInterface ...$responses) {
        $this->responses = $responses;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Generator|ResponseInterface[]
     */
    public function getIterator() {
        return new ArrayIterator($this->responses);
    }
}
