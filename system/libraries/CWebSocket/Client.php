<?php
use React\EventLoop\Loop;
use React\Promise\Deferred;
use GuzzleHttp\Psr7 as gPsr;
use React\EventLoop\LoopInterface;
use React\Promise\RejectedPromise;
use React\Socket\ConnectorInterface;
use React\Socket\ConnectionInterface;
use Psr\Http\Message\RequestInterface;
use Ratchet\RFC6455\Handshake\ClientNegotiator;

class CWebSocket_Client {
    protected $loop;

    protected $connector;

    protected $negotiator;

    public function __construct(LoopInterface $loop = null, ConnectorInterface $connector = null) {
        $this->loop = $loop ?: Loop::get();

        if (null === $connector) {
            $connector = new \React\Socket\Connector($this->loop, [
                'timeout' => 20
            ]);
        }

        $this->connector = $connector;
        $this->negotiator = new ClientNegotiator();
    }

    /**
     * @param string $url
     * @param array  $subProtocols
     * @param array  $headers
     *
     * @return \React\Promise\PromiseInterface
     */
    public function connect($url, array $subProtocols = [], array $headers = []) {
        try {
            $request = $this->generateRequest($url, $subProtocols, $headers);
            $uri = $request->getUri();
        } catch (\Exception $e) {
            return new RejectedPromise($e);
        }
        $secure = 'wss' === substr($url, 0, 3);
        $connector = $this->connector;

        $port = $uri->getPort() ?: ($secure ? 443 : 80);

        $scheme = $secure ? 'tls' : 'tcp';

        $uriString = $scheme . '://' . $uri->getHost() . ':' . $port;

        $connecting = $connector->connect($uriString);

        $futureWsConn = new Deferred(function ($_, $reject) use ($url, $connecting) {
            $reject(new \RuntimeException(
                'Connection to ' . $url . ' cancelled during handshake'
            ));

            // either close active connection or cancel pending connection attempt
            $connecting->then(function (ConnectionInterface $connection) {
                $connection->close();
            });
            $connecting->cancel();
        });

        $connecting->then(function (ConnectionInterface $conn) use ($request, $subProtocols, $futureWsConn) {
            $earlyClose = function () use ($futureWsConn) {
                $futureWsConn->reject(new \RuntimeException('Connection closed before handshake'));
            };

            $stream = $conn;

            $stream->on('close', $earlyClose);
            $futureWsConn->promise()->then(function () use ($stream, $earlyClose) {
                $stream->removeListener('close', $earlyClose);
            });

            $buffer = '';
            $headerParser = function ($data) use ($stream, &$headerParser, &$buffer, $futureWsConn, $request, $subProtocols) {
                $buffer .= $data;
                if (false == strpos($buffer, "\r\n\r\n")) {
                    return;
                }

                $stream->removeListener('data', $headerParser);

                $response = gPsr\Message::parseResponse($buffer);

                if (!$this->negotiator->validateResponse($request, $response)) {
                    $futureWsConn->reject(new \DomainException(gPsr\Message::toString($response)));
                    $stream->close();

                    return;
                }

                $acceptedProtocol = $response->getHeader('Sec-WebSocket-Protocol');
                if ((count($subProtocols) > 0) && 1 !== count(array_intersect($subProtocols, $acceptedProtocol))) {
                    $futureWsConn->reject(new \DomainException('Server did not respond with an expected Sec-WebSocket-Protocol'));
                    $stream->close();

                    return;
                }

                $futureWsConn->resolve(new CWebSocket_Client_WebSocket($stream, $response, $request));

                $futureWsConn->promise()->then(function (CWebSocket_Client_WebSocket $conn) use ($stream) {
                    $stream->emit('data', [$conn->response->getBody(), $stream]);
                });
            };

            $stream->on('data', $headerParser);
            $stream->write(gPsr\Message::toString($request));
        }, [$futureWsConn, 'reject']);

        return $futureWsConn->promise();
    }

    /**
     * @param string $url
     * @param array  $subProtocols
     * @param array  $headers
     *
     * @throws \InvalidArgumentException
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function generateRequest($url, array $subProtocols, array $headers) {
        $uri = gPsr\Utils::uriFor($url);

        $scheme = $uri->getScheme();

        if (!in_array($scheme, ['ws', 'wss'])) {
            throw new \InvalidArgumentException(sprintf('Cannot connect to invalid URL (%s)', $url));
        }

        $uri = $uri->withScheme('wss' === $scheme ? 'HTTPS' : 'HTTP');

        $headers += ['User-Agent' => 'Ratchet-Pawl/0.4.1'];

        $request = array_reduce(array_keys($headers), function (RequestInterface $request, $header) use ($headers) {
            return $request->withHeader($header, $headers[$header]);
        }, $this->negotiator->generateRequest($uri));

        if (!$request->getHeader('Origin')) {
            $request = $request->withHeader('Origin', str_replace('ws', 'http', $scheme) . '://' . $uri->getHost());
        }

        if (count($subProtocols) > 0) {
            $protocols = implode(',', $subProtocols);
            if ($protocols != '') {
                $request = $request->withHeader('Sec-WebSocket-Protocol', $protocols);
            }
        }

        return $request;
    }
}
