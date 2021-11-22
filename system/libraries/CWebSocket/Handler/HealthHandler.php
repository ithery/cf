<?php

use GuzzleHttp\Psr7\Response;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;
use Psr\Http\Message\RequestInterface;

class CWebSocket_Handler_HealthHandler implements HttpServerInterface {
    /**
     * Handle the socket opening.
     *
     * @param \Ratchet\ConnectionInterface       $connection
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    public function onOpen(ConnectionInterface $connection, RequestInterface $request = null) {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['ok' => true])
        );

        c::tap($connection)->send(\GuzzleHttp\Psr7\str($response))->close();
    }

    /**
     * Handle the incoming message.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $message
     *
     * @return void
     */
    public function onMessage(ConnectionInterface $connection, $message) {
    }

    /**
     * Handle the websocket close.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function onClose(ConnectionInterface $connection) {
    }

    /**
     * Handle the websocket errors.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param WebSocketException           $exception
     *
     * @return void
     */
    public function onError(ConnectionInterface $connection, Exception $exception) {
    }
}
