<?php

final class CVendor_Firebase_Http_HttpClientOptions {
    /**
     * The amount of seconds to wait while connecting to a server.
     *
     * Defaults to indefinitely.
     *
     * @var null|float
     */
    private $connectTimeout = null;

    /**
     * The amount of seconds to wait while reading a streamed body.
     *
     * Defaults to the value of the default_socket_timeout PHP ini setting.
     *
     * @var null|float
     */
    private $readTimeout = null;

    /**
     * The amount of seconds to wait for a full request (connect + transfer + read) to complete.
     *
     * Defaults to indefinitely.
     *
     * @var null|float
     */
    private $timeout = null;

    /**
     * The proxy that all requests should be passed through.
     *
     * @var null|string
     */
    private $proxy = null;

    private function __construct() {
    }

    /**
     * @return self
     */
    public static function defaultOptions() {
        return new self();
    }

    /**
     * The amount of seconds to wait while connecting to a server.
     *
     * Defaults to indefinitely.
     *
     * @return null|float
     */
    public function connectTimeout() {
        return $this->connectTimeout;
    }

    /**
     * @param float $value the amount of seconds to wait while connecting to a server
     *
     * @return self
     */
    public function withConnectTimeout($value) {
        if ($value < 0) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('The connect timeout cannot be smaller than zero.');
        }

        $options = clone $this;
        $options->connectTimeout = $value;

        return $options;
    }

    /**
     * The amount of seconds to wait while reading a streamed body.
     *
     * Defaults to the value of the default_socket_timeout PHP ini setting.
     *
     * @return null|float
     */
    public function readTimeout() {
        return $this->readTimeout;
    }

    /**
     * @param float $value the amount of seconds to wait while reading a streamed body
     *
     * @return self
     */
    public function withReadTimeout($value) {
        if ($value < 0) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('The read timeout cannot be smaller than zero.');
        }

        $options = clone $this;
        $options->readTimeout = $value;

        return $options;
    }

    /**
     * The amount of seconds to wait for a full request (connect + transfer + read) to complete.
     *
     * Defaults to indefinitely.
     *
     * @return null|float
     */
    public function timeout() {
        return $this->timeout;
    }

    /**
     * @param float $value the amount of seconds to wait while reading a streamed body
     *
     * @return self
     */
    public function withTimeout($value) {
        if ($value < 0) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('The total timeout cannot be smaller than zero.');
        }

        $options = clone $this;
        $options->timeout = $value;

        return $options;
    }

    /**
     * The proxy that all requests should be passed through.
     *
     * @return null|string
     */
    public function proxy() {
        return $this->proxy;
    }

    /**
     * @param string $value the proxy that all requests should be passed through
     *
     * @return self
     */
    public function withProxy($value) {
        $options = clone $this;
        $options->proxy = $value;

        return $options;
    }
}
