<?php

use Psr\Http\Message\RequestInterface;

final class CVendor_Firebase_Http_Requests implements IteratorAggregate {
    /** @var RequestInterface[] */
    private $requests;

    public function __construct(RequestInterface ...$requests) {
        $this->requests = $requests;
    }

    /**
     * @return RequestInterface|null
     */
    public function findBy(callable $callable) {
        $results = \array_filter($this->requests, $callable);

        return \array_shift($results) ?: null;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Generator|RequestInterface[]
     */
    public function getIterator() {
        return new ArrayIterator($this->requests);
    }
}
