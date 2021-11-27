<?php

use Psr\Http\Message\RequestInterface;

class CWebSocket_Server_QueryParameter {
    /**
     * The Request object.
     *
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    public static function create(RequestInterface $request) {
        return new static($request);
    }

    /**
     * Initialize the class.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    public function __construct(RequestInterface $request) {
        $this->request = $request;
    }

    /**
     * Get all query parameters.
     *
     * @return array
     */
    public function all() {
        $queryParameters = [];

        parse_str($this->request->getUri()->getQuery(), $queryParameters);

        return $queryParameters;
    }

    /**
     * Get a specific query parameter.
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function get($name, $default = '') {
        return isset($this->all()[$name]) ? $this->all()[$name] : $default;
    }
}
