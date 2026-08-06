<?php

use PHPUnit\Framework\TestCase;

/**
 * `curl_multi_init()` used to be assigned straight into an undeclared
 * `$_mh` property inside __get() (both GuzzleHttp\Handler\CurlMultiHandler
 * and GuzzleHttp\Ring\Client\CurlMultiHandler), which PHP 8.2+ deprecates
 * as a dynamic property write. Fires on every outbound HTTP call through
 * Guzzle, so it was the noisiest deprecation in the framework. Fixed by
 * declaring the property and unset()-ing it right after construction, so
 * __get() still does the lazy init exactly once - this asserts that both
 * classes stay lazy-init-once and free of the deprecation, using
 * Closure::bind() to read $_mh from the class's own scope the same way
 * tick()/execute() do internally (reading a private property from outside
 * the class always goes through __get(), so that wouldn't prove anything).
 */
class GuzzleCurlMultiHandlerDeprecationTest extends TestCase {
    private function assertNoDeprecation(callable $fn) {
        $captured = [];
        set_error_handler(function ($errno, $errstr) use (&$captured) {
            $captured[] = $errstr;

            return true;
        });

        try {
            $fn();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $captured, 'Expected no warnings/deprecations, got: ' . implode('; ', $captured));
    }

    public function testHandlerCurlMultiHandlerLazyInitsWithoutDeprecation() {
        $this->assertNoDeprecation(function () {
            $handler = new GuzzleHttp\Handler\CurlMultiHandler(['handle_factory' => new stdClass()]);
            $read = Closure::bind(function () {
                return [$this->_mh, $this->_mh];
            }, $handler, GuzzleHttp\Handler\CurlMultiHandler::class);
            [$first, $second] = $read();

            $this->assertSame($first, $second);
        });
    }

    public function testRingCurlMultiHandlerLazyInitsWithoutDeprecation() {
        $this->assertNoDeprecation(function () {
            $handler = new GuzzleHttp\Ring\Client\CurlMultiHandler(['handle_factory' => function () {}]);
            $read = Closure::bind(function () {
                return [$this->_mh, $this->_mh];
            }, $handler, GuzzleHttp\Ring\Client\CurlMultiHandler::class);
            [$first, $second] = $read();

            $this->assertSame($first, $second);
        });
    }

    public function testRingCurlMultiHandlerHonorsAnExplicitlyProvidedHandleWithoutDeprecation() {
        $existing = curl_multi_init();

        $this->assertNoDeprecation(function () use ($existing) {
            $handler = new GuzzleHttp\Ring\Client\CurlMultiHandler(['mh' => $existing, 'handle_factory' => function () {}]);
            $read = Closure::bind(function () {
                return $this->_mh;
            }, $handler, GuzzleHttp\Ring\Client\CurlMultiHandler::class);

            $this->assertSame($existing, $read());
        });
    }
}
