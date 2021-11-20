<?php

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class CWebSocket_Handler_WebSocketHandler implements MessageComponentInterface {
    /**
     * The channel manager.
     *
     * @var CWebSocket_Contract_ChannelManagerInterface
     */
    protected $channelManager;

    /**
     * Initialize a new handler.
     *
     * @return void
     */
    public function __construct() {
        $this->channelManager = CWebSocket::channelManager();
    }

    /**
     * Handle the socket opening.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function onOpen(ConnectionInterface $connection) {
        if (!$this->connectionCanBeMade($connection)) {
            return $connection->close();
        }
        $this->verifyAppKey($connection)
            ->verifyOrigin($connection)
            ->limitConcurrentConnections($connection)
            ->generateSocketId($connection)
            ->establishConnection($connection);
        if (isset($connection->app)) {
            /** @var \GuzzleHttp\Psr7\Request $request */
            $request = $connection->httpRequest;

            if ($connection->app->statisticsEnabled) {
                CWebSocket::statisticCollector()->connection($connection->app->id);
            }

            $this->channelManager->subscribeToApp($connection->app->id);
            $this->channelManager->connectionPonged($connection);

            CWebSocket_DashboardLogger::log($connection->app->id, CWebSocket_DashboardLogger::TYPE_CONNECTED, [
                'origin' => "{$request->getUri()->getScheme()}://{$request->getUri()->getHost()}",
                'socketId' => $connection->socketId,
            ]);
            CWebSocket_Event_NewConnection::dispatch($connection->app->id, $connection->socketId);
        }
    }

    /**
     * Handle the incoming message.
     *
     * @param \Ratchet\ConnectionInterface                $connection
     * @param \Ratchet\RFC6455\Messaging\MessageInterface $message
     *
     * @return void
     */
    public function onMessage(ConnectionInterface $connection, MessageInterface $message) {
        if (!isset($connection->app)) {
            return;
        }

        CWebSocket_PusherMessageFactory::createForMessage(
            $message,
            $connection,
            $this->channelManager
        )->respond();

        if ($connection->app->statisticsEnabled) {
            CWebSocket::statisticCollector()->webSocketMessage($connection->app->id);
        }

        CWebSocket_Event_MessageReceived::dispatch(
            $connection->app->id,
            $connection->socketId,
            $message
        );
    }

    /**
     * Handle the websocket close.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function onClose(ConnectionInterface $connection) {
        $this->channelManager
            ->unsubscribeFromAllChannels($connection)
            ->then(function (bool $unsubscribed) use ($connection) {
                if (isset($connection->app)) {
                    if ($connection->app->statisticsEnabled) {
                        CWebSocket::statisticCollector()->disconnection($connection->app->id);
                    }

                    $this->channelManager->unsubscribeFromApp($connection->app->id);

                    CWebSocket_DashboardLogger::log($connection->app->id, CWebSocket_DashboardLogger::TYPE_DISCONNECTED, [
                        'socketId' => $connection->socketId,
                    ]);

                    CWebSocket_Event_ConnectionClosed::dispatch($connection->app->id, $connection->socketId);
                }
            });
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
        if ($exception instanceof CWebSocket_Exception) {
            $connection->send(json_encode(
                $exception->getPayload()
            ));
        }
    }

    /**
     * Check if the connection can be made for the
     * current server instance.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return bool
     */
    protected function connectionCanBeMade(ConnectionInterface $connection) {
        return $this->channelManager->acceptsNewConnections();
    }

    /**
     * Verify the app key validity.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return $this
     */
    protected function verifyAppKey(ConnectionInterface $connection) {
        $query = CWebSocket_Server_QueryParameter::create($connection->httpRequest);

        $appKey = $query->get('appKey');

        if (!$app = CWebSocket_App::findByKey($appKey)) {
            throw new CWebSocket_Exception_UnknownAppKey($appKey);
        }

        $connection->app = $app;

        return $this;
    }

    /**
     * Verify the origin.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return $this
     */
    protected function verifyOrigin(ConnectionInterface $connection) {
        if (!$connection->app->allowedOrigins) {
            return $this;
        }

        $header = (string) (isset($connection->httpRequest->getHeader('Origin')[0]) ? $connection->httpRequest->getHeader('Origin')[0] : null);

        $origin = parse_url($header, PHP_URL_HOST) ?: $header;

        if (!$header || !in_array($origin, $connection->app->allowedOrigins)) {
            throw new CWebSocket_Exception_OriginNotAllowed($connection->app->key);
        }

        return $this;
    }

    /**
     * Limit the connections count by the app.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return $this
     */
    protected function limitConcurrentConnections(ConnectionInterface $connection) {
        if (!is_null($capacity = $connection->app->capacity)) {
            $this->channelManager
                ->getGlobalConnectionsCount($connection->app->id)
                ->then(function ($connectionsCount) use ($capacity, $connection) {
                    if ($connectionsCount >= $capacity) {
                        $exception = new CWebSocket_Exception_ConnectionsOverCapacity();

                        $payload = json_encode($exception->getPayload());

                        c::tap($connection)->send($payload)->close();
                    }
                });
        }

        return $this;
    }

    /**
     * Create a socket id.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return $this
     */
    protected function generateSocketId(ConnectionInterface $connection) {
        $socketId = sprintf('%d.%d', random_int(1, 1000000000), random_int(1, 1000000000));

        $connection->socketId = $socketId;

        return $this;
    }

    /**
     * Establish connection with the client.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return $this
     */
    protected function establishConnection(ConnectionInterface $connection) {
        $connection->send(json_encode([
            'event' => 'pusher:connection_established',
            'data' => json_encode([
                'socket_id' => $connection->socketId,
                'activity_timeout' => 30,
            ]),
        ]));

        return $this;
    }
}
