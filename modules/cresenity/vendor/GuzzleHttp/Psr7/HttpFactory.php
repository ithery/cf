<?php

//declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Implements all of the PSR-17 interfaces.
 *
 * Note: in consuming code it is recommended to require the implemented interfaces
 * and inject the instance of this class multiple times.
 */
final class HttpFactory implements
RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface {

    public function createUploadedFile(
            StreamInterface $stream,
            $size = null,
            $error = \UPLOAD_ERR_OK,
            $clientFilename = null,
            $clientMediaType = null
    ) {
        if ($size === null) {
            $size = $stream->getSize();
        }

        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    public function createStream($content = '') {
        return \GuzzleHttp\Psr7\stream_for($content);
    }

    public function createStreamFromFile($file, $mode = 'r') {
        try {
            $resource = \GuzzleHttp\Psr7\try_fopen($file, $mode);
        } catch (\RuntimeException $e) {
            if ('' === $mode || false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], true)) {
                throw new \InvalidArgumentException(sprintf('Invalid file opening mode "%s"', $mode), 0, $e);
            }

            throw $e;
        }

        return \GuzzleHttp\Psr7\stream_for($resource);
    }

    public function createStreamFromResource($resource) {
        return \GuzzleHttp\Psr7\stream_for($resource);
    }

    public function createServerRequest($method, $uri, array $serverParams = []) {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }

        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }

    public function createResponse($code = 200, $reasonPhrase = '') {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }

    public function createRequest($method, $uri) {
        return new Request($method, $uri);
    }

    public function createUri($uri = '') {
        return new Uri($uri);
    }

}
