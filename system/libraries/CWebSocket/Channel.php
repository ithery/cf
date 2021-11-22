<?php

use Ratchet\ConnectionInterface;

class CWebsocket_Channel {
    /**
     * The channel name.
     *
     * @var string
     */
    protected $name;

    /**
     * The connections that got subscribed to this channel.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * Create a new instance.
     *
     * @param string $name
     *
     * @return void
     */
    public function __construct($name) {
        $this->name = $name;
        $this->channelManager = CWebSocket::channelManager();
    }

    /**
     * Get channel name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the list of subscribed connections.
     *
     * @return array
     */
    public function getConnections() {
        return $this->connections;
    }

    /**
     * Check if the channel has connections.
     *
     * @return bool
     */
    public function hasConnections() {
        return count($this->getConnections()) > 0;
    }

    /**
     * Add a new connection to the channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \stdClass                    $payload
     *
     * @see    https://pusher.com/docs/pusher_protocol#presence-channel-events
     *
     * @return bool
     */
    public function subscribe(ConnectionInterface $connection, $payload) {
        $this->saveConnection($connection);

        $connection->send(json_encode([
            'event' => 'pusher_internal:subscription_succeeded',
            'channel' => $this->getName(),
        ]));

        CWebSocket_DashboardLogger::log($connection->app->id, CWebSocket_DashboardLogger::TYPE_SUBSCRIBED, [
            'socketId' => $connection->socketId,
            'channel' => $this->getName(),
        ]);

        CWebSocket_Event_SubscribedToChannel::dispatch(
            $connection->app->id,
            $connection->socketId,
            $this->getName()
        );

        return true;
    }

    /**
     * Unsubscribe connection from the channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return bool
     */
    public function unsubscribe(ConnectionInterface $connection) {
        if (!$this->hasConnection($connection)) {
            return false;
        }

        unset($this->connections[$connection->socketId]);

        CWebSocket_Event_UnsubscribedFromChannel::dispatch(
            $connection->app->id,
            $connection->socketId,
            $this->getName()
        );

        return true;
    }

    /**
     * Check if the given connection exists.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return bool
     */
    public function hasConnection(ConnectionInterface $connection) {
        return isset($this->connections[$connection->socketId]);
    }

    /**
     * Store the connection to the subscribers list.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function saveConnection(ConnectionInterface $connection) {
        $this->connections[$connection->socketId] = $connection;
    }

    /**
     * Broadcast a payload to the subscribed connections.
     *
     * @param string|int $appId
     * @param \stdClass  $payload
     * @param bool       $replicate
     *
     * @return bool
     */
    public function broadcast($appId, $payload, $replicate = true) {
        c::collect($this->getConnections())
            ->each(function ($connection) use ($payload) {
                $connection->send(json_encode($payload));
            });

        if ($replicate) {
            $this->channelManager->broadcastAcrossServers($appId, null, $this->getName(), $payload);
        }

        return true;
    }

    /**
     * Broadcast a payload to the locally-subscribed connections.
     *
     * @param string|int $appId
     * @param \stdClass  $payload
     *
     * @return bool
     */
    public function broadcastLocally($appId, $payload) {
        return $this->broadcast($appId, $payload, false);
    }

    /**
     * Broadcast the payload, but exclude a specific socket id.
     *
     * @param \stdClass   $payload
     * @param null|string $socketId
     * @param string|int  $appId
     * @param bool        $replicate
     *
     * @return bool
     */
    public function broadcastToEveryoneExcept($payload, $socketId, $appId, $replicate = true) {
        if ($replicate) {
            $this->channelManager->broadcastAcrossServers($appId, $socketId, $this->getName(), $payload);
        }

        if (is_null($socketId)) {
            return $this->broadcast($appId, $payload, $replicate);
        }

        c::collect($this->getConnections())->each(function (ConnectionInterface $connection) use ($socketId, $payload) {
            if ($connection->socketId !== $socketId) {
                $connection->send(json_encode($payload));
            }
        });

        return true;
    }

    /**
     * Broadcast the payload, but exclude a specific socket id.
     *
     * @param \stdClass   $payload
     * @param null|string $socketId
     * @param string|int  $appId
     *
     * @return bool
     */
    public function broadcastLocallyToEveryoneExcept($payload, $socketId, $appId) {
        return $this->broadcastToEveryoneExcept(
            $payload,
            $socketId,
            $appId,
            false
        );
    }

    /**
     * Check if the signature for the payload is valid.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \stdClass                    $payload
     *
     * @throws CWebSocket_Exception_InvalidSignature
     *
     * @return void
     */
    protected function verifySignature(ConnectionInterface $connection, $payload) {
        $signature = "{$connection->socketId}:{$this->getName()}";

        if (isset($payload->channel_data)) {
            $signature .= ":{$payload->channel_data}";
        }

        if (!hash_equals(
            hash_hmac('sha256', $signature, $connection->app->secret),
            cstr::after($payload->auth, ':')
        )
        ) {
            throw new CWebSocket_Exception_InvalidSignature();
        }
    }
}
