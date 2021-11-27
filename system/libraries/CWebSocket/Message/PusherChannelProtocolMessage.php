<?php

use Ratchet\ConnectionInterface;

class CWebSocket_Message_PusherChannelProtocolMessage extends CWebSocket_Message_PusherClientMessage {
    /**
     * Respond with the payload.
     *
     * @return void
     */
    public function respond() {
        $eventName = cstr::camel(cstr::after($this->payload->event, ':'));

        if (method_exists($this, $eventName) && $eventName !== 'respond') {
            call_user_func([$this, $eventName], $this->connection, $this->payload->data ?: new stdClass());
        }
    }

    /**
     * Ping the connection.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @see    https://pusher.com/docs/pusher_protocol#ping-pong
     *
     * @return void
     */
    protected function ping(ConnectionInterface $connection) {
        $this->channelManager
            ->connectionPonged($connection)
            ->then(function () use ($connection) {
                $connection->send(json_encode(['event' => 'pusher:pong']));

                CWebSocket_Event_ConnectionPonged::dispatch($connection->app->id, $connection->socketId);
            });
    }

    /**
     * Subscribe to channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \stdClass                    $payload
     *
     * @see    https://pusher.com/docs/pusher_protocol#pusher-subscribe
     *
     * @return void
     */
    protected function subscribe(ConnectionInterface $connection, stdClass $payload) {
        $this->channelManager->subscribeToChannel($connection, $payload->channel, $payload);
    }

    /**
     * Unsubscribe from the channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \stdClass                    $payload
     *
     * @return void
     */
    public function unsubscribe(ConnectionInterface $connection, stdClass $payload) {
        $this->channelManager->unsubscribeFromChannel($connection, $payload->channel, $payload);
    }
}
