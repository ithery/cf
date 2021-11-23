<?php

use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;

interface CWebSocket_Contract_ChannelManagerInterface {
    /**
     * Create a new channel manager instance.
     *
     * @param LoopInterface $loop
     * @param null|string   $factoryClass
     *
     * @return void
     */
    public function __construct(LoopInterface $loop, $factoryClass = null);

    /**
     * Find the channel by app & name.
     *
     * @param string|int $appId
     * @param string     $channel
     *
     * @return null|CWebSocket_Channel
     */
    public function find($appId, $channel);

    /**
     * Find a channel by app & name or create one.
     *
     * @param string|int $appId
     * @param string     $channel
     *
     * @return CWebSocket_Channel
     */
    public function findOrCreate($appId, $channel);

    /**
     * Get the local connections, regardless of the channel
     * they are connected to.
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getLocalConnections();

    /**
     * Get all channels for a specific app
     * for the current instance.
     *
     * @param string|int $appId
     *
     * @return \React\Promise\PromiseInterface[array]
     */
    public function getLocalChannels($appId);

    /**
     * Get all channels for a specific app
     * across multiple servers.
     *
     * @param string|int $appId
     *
     * @return \React\Promise\PromiseInterface[array]
     */
    public function getGlobalChannels($appId);

    /**
     * Remove connection from all channels.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return PromiseInterface[bool]
     */
    public function unsubscribeFromAllChannels(ConnectionInterface $connection);

    /**
     * Subscribe the connection to a specific channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $channelName
     * @param stdClass                     $payload
     *
     * @return PromiseInterface[bool]
     */
    public function subscribeToChannel(ConnectionInterface $connection, $channelName, $payload);

    /**
     * Unsubscribe the connection from the channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $channelName
     * @param stdClass                     $payload
     *
     * @return PromiseInterface[bool]
     */
    public function unsubscribeFromChannel(ConnectionInterface $connection, $channelName, $payload);

    /**
     * Subscribe the connection to a specific channel, returning
     * a promise containing the amount of connections.
     *
     * @param string|int $appId
     *
     * @return PromiseInterface[int]
     */
    public function subscribeToApp($appId);

    /**
     * Unsubscribe the connection from the channel, returning
     * a promise containing the amount of connections after decrement.
     *
     * @param string|int $appId
     *
     * @return PromiseInterface[int]
     */
    public function unsubscribeFromApp($appId);

    /**
     * Get the connections count on the app
     * for the current server instance.
     *
     * @param string|int  $appId
     * @param null|string $channelName
     *
     * @return PromiseInterface[int]
     */
    public function getLocalConnectionsCount($appId, $channelName = null);

    /**
     * Get the connections count
     * across multiple servers.
     *
     * @param string|int  $appId
     * @param null|string $channelName
     *
     * @return PromiseInterface[int]
     */
    public function getGlobalConnectionsCount($appId, $channelName = null);

    /**
     * Broadcast the message across multiple servers.
     *
     * @param string|int  $appId
     * @param null|string $socketId
     * @param string      $channel
     * @param stdClass    $payload
     * @param null|string $serverId
     *
     * @return PromiseInterface[bool]
     */
    public function broadcastAcrossServers($appId, $socketId, $channel, $payload, $serverId = null);

    /**
     * Handle the user when it joined a presence channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param stdClass                     $user
     * @param string                       $channel
     * @param stdClass                     $payload
     *
     * @return PromiseInterface[bool]
     */
    public function userJoinedPresenceChannel(ConnectionInterface $connection, $user, $channel, $payload);

    /**
     * Handle the user when it left a presence channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param stdClass                     $user
     * @param string                       $channel
     *
     * @return PromiseInterface[bool]
     */
    public function userLeftPresenceChannel(ConnectionInterface $connection, $user, $channel);

    /**
     * Get the presence channel members.
     *
     * @param string|int $appId
     * @param string     $channel
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getChannelMembers($appId, $channel);

    /**
     * Get a member from a presence channel based on connection.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $channel
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getChannelMember(ConnectionInterface $connection, $channel);

    /**
     * Get the presence channels total members count.
     *
     * @param string|int $appId
     * @param array      $channelNames
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getChannelsMembersCount($appId, array $channelNames);

    /**
     * Get the socket IDs for a presence channel member.
     *
     * @param string|int $userId
     * @param string|int $appId
     * @param string     $channelName
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getMemberSockets($userId, $appId, $channelName);

    /**
     * Keep tracking the connections availability when they pong.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return PromiseInterface[bool]
     */
    public function connectionPonged(ConnectionInterface $connection);

    /**
     * Remove the obsolete connections that didn't ponged in a while.
     *
     * @return PromiseInterface[bool]
     */
    public function removeObsoleteConnections();
}
