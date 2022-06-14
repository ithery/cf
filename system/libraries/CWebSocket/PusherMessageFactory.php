<?php

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

class CWebSocket_PusherMessageFactory {
    /**
     * Create a new message.
     *
     * @param \Ratchet\RFC6455\Messaging\MessageInterface  $message
     * @param \Ratchet\ConnectionInterface                 $connection
     * @param \CWebSocket_Contract_ChannelManagerInterface $channelManager
     *
     * @return CWebSocket_Contract_PusherMessageInterface
     */
    public static function createForMessage(
        MessageInterface $message,
        ConnectionInterface $connection,
        CWebSocket_Contract_ChannelManagerInterface $channelManager
    ) {
        $payload = json_decode($message->getPayload());

        return cstr::startsWith($payload->event, 'pusher:')
            ? new CWebSocket_Message_PusherChannelProtocolMessage($payload, $connection, $channelManager)
            : new CWebSocket_Message_PusherClientMessage($payload, $connection, $channelManager);
    }
}
