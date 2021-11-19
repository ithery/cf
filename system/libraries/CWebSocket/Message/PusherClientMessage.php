<?php

use Ratchet\ConnectionInterface;
use CWebSocket_Contract_PusherMessageInterface;
use CWebSocket_Contract_ChannelManagerInterface;

class CWebSocket_Message_PusherClientMessage implements CWebSocket_Contract_PusherMessageInterface {
    /**
     * The payload to send.
     *
     * @var \stdClass
     */
    protected $payload;

    /**
     * The socket connection.
     *
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * The channel manager.
     *
     * @var CWebSocket_Contract_ChannelManagerInterface
     */
    protected $channelManager;

    /**
     * Create a new instance.
     *
     * @param \stdClass                                   $payload
     * @param \Ratchet\ConnectionInterface                $connection
     * @param CWebSocket_Contract_ChannelManagerInterface $channelManager
     */
    public function __construct($payload, ConnectionInterface $connection, CWebSocket_Contract_ChannelManagerInterface $channelManager) {
        $this->payload = $payload;
        $this->connection = $connection;
        $this->channelManager = $channelManager;
    }

    /**
     * Respond to the message construction.
     *
     * @return void
     */
    public function respond() {
        if (!cstr::startsWith($this->payload->event, 'client-')) {
            return;
        }

        if (!$this->connection->app->clientMessagesEnabled) {
            return;
        }

        $channel = $this->channelManager->find(
            $this->connection->app->id,
            $this->payload->channel
        );

        c::optional($channel)->broadcastToEveryoneExcept(
            $this->payload,
            $this->connection->socketId,
            $this->connection->app->id
        );

        CWebSocket_DashboardLogger::log($this->connection->app->id, CWebSocket_DashboardLogger::TYPE_WS_MESSAGE, [
            'socketId' => $this->connection->socketId,
            'event' => $this->payload->event,
            'channel' => $this->payload->channel,
            'data' => $this->payload,
        ]);
    }
}
