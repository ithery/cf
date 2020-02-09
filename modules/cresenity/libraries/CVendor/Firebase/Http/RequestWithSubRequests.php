<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Psr7\AppendStream;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * This is basically a Multipart Request, except that in the parts the sub request start lines
 * are injected between the headers and the body.
 *
 * Header: value
 * Header: value
 *
 * method requestTarget protocolVersion
 *
 * body
 */
final class CVendor_Firebase_Http_RequestWithSubRequests implements CVendor_Firebase_Http_HasSubRequestsInterface, RequestInterface {

    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $boundary;

    /** @var array */
    private $headers;

    /** @var AppendStream */
    private $body;

    /** @var Requests */
    private $subRequests;

    /**
     * @param string|UriInterface $uri
     * @param string $version Protocol version
     */
    public function __construct($uri, CVendor_Firebase_Http_Requests $subRequests, $version = '1.1') {
        $this->boundary = \sha1(\uniqid('', true));

        $headers = [
            'Content-Type' => 'multipart/mixed; boundary=' . $this->boundary,
        ];

        $this->body = new AppendStream();

        $this->subRequests = $subRequests;

        foreach ($subRequests as $request) {
            $this->appendPartForSubRequest($request);
        }
        $this->appendStream("--{$this->boundary}--");

        $request = new Request($this->method, $uri, $headers, $this->body, $version);

        $contentLength = $request->getBody()->getSize();
        if ($contentLength !== null) {
            $request = $request->withHeader('Content-Length', (string) $contentLength);
        }

        $this->wrappedRequest = $request;
    }

    public function subRequests() {
        return $this->subRequests;
    }

    private function appendPartForSubRequest(RequestInterface $subRequest) {
        $this->appendStream("--{$this->boundary}\r\n");
        $this->appendStream($this->subRequestHeadersAsString($subRequest) . "\r\n\r\n");
        $this->appendStream("{$subRequest->getMethod()} {$subRequest->getRequestTarget()} HTTP/{$subRequest->getProtocolVersion()}\r\n\r\n");
        $this->appendStream($subRequest->getBody() . "\r\n");
    }

    private function appendStream($value) {
        // Objects are passed by reference, we want to ensure that they are not changed
        if ($value instanceof StreamInterface) {
            $value = (string) $value;
        }

        $this->body->addStream(stream_for($value));
    }

    private function subRequestHeadersAsString(RequestInterface $request) {
        $headerNames = \array_keys($request->getHeaders());

        $headers = [];

        foreach ($headerNames as $name) {
            if (\mb_strtolower($name) === 'host') {
                continue;
            }
            $headers[] = "{$name}: {$request->getHeaderLine($name)}";
        }

        return \implode("\r\n", $headers);
    }

}
