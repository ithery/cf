<?php
use Ratchet\ConnectionInterface;

class CWebSocket_Channel_PrivateChannel extends CWebSocket_Channel {
    /**
     * Subscribe to the channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \stdClass                    $payload
     *
     * @throws InvalidSignature
     *
     * @see    https://pusher.com/docs/pusher_protocol#presence-channel-events
     *
     * @return bool
     */
    public function subscribe(ConnectionInterface $connection, $payload) {
        $this->verifySignature($connection, $payload);

        return parent::subscribe($connection, $payload);
    }
}
