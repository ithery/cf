<?php

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Uri;
use Nyholm\Psr7\Stream;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\UploadedFile;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 *
 * @final This class should never be extended. See https://github.com/Nyholm/psr7/blob/master/doc/final.md
 */
class Psr17Factory implements RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface {
    public function createRequest($method, $uri) {
        return new Request($method, $uri);
    }

    public function createResponse($code = 200, $reasonPhrase = '') {
        if (2 > \func_num_args()) {
            // This will make the Response class to use a custom reasonPhrase
            $reasonPhrase = null;
        }

        return new Response($code, [], null, '1.1', $reasonPhrase);
    }

    public function createStream($content = '') {
        return Stream::create($content);
    }

    public function createStreamFromFile($filename, $mode = 'r') {
        if ('' === $filename) {
            throw new \RuntimeException('Path cannot be empty');
        }

        if (false === $resource = @\fopen($filename, $mode)) {
            if ('' === $mode || false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], true)) {
                throw new \InvalidArgumentException(\sprintf('The mode "%s" is invalid.', $mode));
            }

            throw new \RuntimeException(\sprintf('The file "%s" cannot be opened: %s', $filename, \error_get_last()['message'] ?? ''));
        }

        return Stream::create($resource);
    }

    public function createStreamFromResource($resource): StreamInterface {
        return Stream::create($resource);
    }

    public function createUploadedFile($stream, $size = null, $error = \UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null): UploadedFileInterface {
        if (null === $size) {
            $size = $stream->getSize();
        }

        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    public function createUri($uri = '') {
        return new Uri($uri);
    }

    public function createServerRequest($method, $uri, $serverParams = []) {
        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
}
