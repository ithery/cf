<?php
use Carbon\Carbon;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;
use BeyondCode\LaravelWebSockets\Channels\Channel;
use BeyondCode\LaravelWebSockets\Channels\PresenceChannel;

class CWebSocket_ChannelManager_LocalChannelManager implements CWebSocket_Contract_ChannelManagerInterface {
    /**
     * The list of stored channels.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The list of users that joined the presence channel.
     *
     * @var array
     */
    protected $users = [];

    /**
     * The list of users by socket and their attached id.
     *
     * @var array
     */
    protected $userSockets = [];

    /**
     * Wether the current instance accepts new connections.
     *
     * @var bool
     */
    protected $acceptsNewConnections = true;

    /**
     * The ArrayStore instance of locks.
     *
     * @var \CCache_Driver_ArrayDriver
     */
    protected $store;

    /**
     * The unique server identifier.
     *
     * @var string
     */
    protected $serverId;

    /**
     * The lock name to use on Array to avoid multiple
     * actions that might lead to multiple processings.
     *
     * @var string
     */
    protected static $lockName = 'cf-websockets:channel-manager:lock';

    /**
     * Create a new channel manager instance.
     *
     * @param LoopInterface $loop
     * @param null|string   $factoryClass
     *
     * @return void
     */
    public function __construct(LoopInterface $loop, $factoryClass = null) {
        $this->store = new CCache_Driver_ArrayDriver([]);
        $this->serverId = cstr::uuid()->__toString();
    }

    /**
     * Find the channel by app & name.
     *
     * @param string|int $appId
     * @param string     $channel
     *
     * @return null|CWebsocket_Channel
     */
    public function find($appId, $channel) {
        return (isset($this->channels[$appId][$channel], $this->channels[$appId][$channel])) ? $this->channels[$appId][$channel] : null;
    }

    /**
     * Find a channel by app & name or create one.
     *
     * @param string|int $appId
     * @param string     $channel
     *
     * @return CWebsocket_Channel
     */
    public function findOrCreate($appId, $channel) {
        if (!$channelInstance = $this->find($appId, $channel)) {
            $class = $this->getChannelClassName($channel);

            $this->channels[$appId][$channel] = new $class($channel);
        }

        return $this->channels[$appId][$channel];
    }

    /**
     * Get the local connections, regardless of the channel
     * they are connected to.
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getLocalConnections() {
        $connections = c::collect($this->channels)
            ->map(function ($channelsWithConnections, $appId) {
                return c::collect($channelsWithConnections)->values();
            })
            ->values()->collapse()
            ->map(function ($channel) {
                return c::collect($channel->getConnections());
            })
            ->values()->collapse()
            ->toArray();

        return CWebSocket_Helper::createFulfilledPromise($connections);
    }

    /**
     * Get all channels for a specific app
     * for the current instance.
     *
     * @param string|int $appId
     *
     * @return \React\Promise\PromiseInterface[array]
     */
    public function getLocalChannels($appId) {
        return CWebSocket_Helper::createFulfilledPromise(
            $this->channels[$appId] ?? []
        );
    }

    /**
     * Get all channels for a specific app
     * across multiple servers.
     *
     * @param string|int $appId
     *
     * @return \React\Promise\PromiseInterface[array]
     */
    public function getGlobalChannels($appId) {
        return $this->getLocalChannels($appId);
    }

    /**
     * Remove connection from all channels.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return PromiseInterface[bool]
     */
    public function unsubscribeFromAllChannels(ConnectionInterface $connection) {
        if (!isset($connection->app)) {
            return CWebSocket_Helper::createFulfilledPromise(false);
        }

        $this->getLocalChannels($connection->app->id)
            ->then(function ($channels) use ($connection) {
                c::collect($channels)
                    ->each(function (CWebSocket_Channel $channel) use ($connection) {
                        $channel->unsubscribe($connection);
                    });

                c::collect($channels)
                    ->reject(function ($channel) {
                        return $channel->hasConnections();
                    })
                    ->each(function (CWebSocket_Channel $channel, string $channelName) use ($connection) {
                        unset($this->channels[$connection->app->id][$channelName]);
                    });
            });

        $this->getLocalChannels($connection->app->id)
            ->then(function ($channels) use ($connection) {
                if (count($channels) === 0) {
                    unset($this->channels[$connection->app->id]);
                }
            });

        return CWebSocket_Helper::createFulfilledPromise(true);
    }

    /**
     * Subscribe the connection to a specific channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $channelName
     * @param stdClass                     $payload
     *
     * @return PromiseInterface[bool]
     */
    public function subscribeToChannel(ConnectionInterface $connection, $channelName, $payload) {
        $channel = $this->findOrCreate($connection->app->id, $channelName);

        return CWebSocket_Helper::createFulfilledPromise(
            $channel->subscribe($connection, $payload)
        );
    }

    /**
     * Unsubscribe the connection from the channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $channelName
     * @param stdClass                     $payload
     *
     * @return PromiseInterface[bool]
     */
    public function unsubscribeFromChannel(ConnectionInterface $connection, $channelName, $payload) {
        $channel = $this->findOrCreate($connection->app->id, $channelName);

        return CWebSocket_Helper::createFulfilledPromise(
            $channel->unsubscribe($connection, $payload)
        );
    }

    /**
     * Subscribe the connection to a specific channel, returning
     * a promise containing the amount of connections.
     *
     * @param string|int $appId
     *
     * @return PromiseInterface[int]
     */
    public function subscribeToApp($appId) {
        return CWebSocket_Helper::createFulfilledPromise(0);
    }

    /**
     * Unsubscribe the connection from the channel, returning
     * a promise containing the amount of connections after decrement.
     *
     * @param string|int $appId
     *
     * @return PromiseInterface[int]
     */
    public function unsubscribeFromApp($appId) {
        return CWebSocket_Helper::createFulfilledPromise(0);
    }

    /**
     * Get the connections count on the app
     * for the current server instance.
     *
     * @param string|int  $appId
     * @param null|string $channelName
     *
     * @return PromiseInterface[int]
     */
    public function getLocalConnectionsCount($appId, $channelName = null) {
        return $this->getLocalChannels($appId)
            ->then(function ($channels) use ($channelName) {
                return c::collect($channels)->when(!is_null($channelName), function ($collection) use ($channelName) {
                    return $collection->filter(function (CWebSocket_Channel $channel) use ($channelName) {
                        return $channel->getName() === $channelName;
                    });
                })
                    ->flatMap(function (CWebSocket_Channel $channel) {
                        return c::collect($channel->getConnections())->pluck('socketId');
                    })
                    ->unique()->count();
            });
    }

    /**
     * Get the connections count
     * across multiple servers.
     *
     * @param string|int  $appId
     * @param null|string $channelName
     *
     * @return PromiseInterface[int]
     */
    public function getGlobalConnectionsCount($appId, $channelName = null) {
        return $this->getLocalConnectionsCount($appId, $channelName);
    }

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
    public function broadcastAcrossServers($appId, $socketId, $channel, $payload, $serverId = null) {
        return CWebSocket_Helper::createFulfilledPromise(true);
    }

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
    public function userJoinedPresenceChannel(ConnectionInterface $connection, $user, $channel, $payload) {
        $this->users["{$connection->app->id}:{$channel}"][$connection->socketId] = json_encode($user);
        $this->userSockets["{$connection->app->id}:{$channel}:{$user->user_id}"][] = $connection->socketId;

        return CWebSocket_Helper::createFulfilledPromise(true);
    }

    /**
     * Handle the user when it left a presence channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param stdClass                     $user
     * @param string                       $channel
     *
     * @return PromiseInterface[bool]
     */
    public function userLeftPresenceChannel(ConnectionInterface $connection, $user, $channel) {
        unset($this->users["{$connection->app->id}:{$channel}"][$connection->socketId]);

        $deletableSocketKey = array_search(
            $connection->socketId,
            $this->userSockets["{$connection->app->id}:{$channel}:{$user->user_id}"]
        );

        if ($deletableSocketKey !== false) {
            unset($this->userSockets["{$connection->app->id}:{$channel}:{$user->user_id}"][$deletableSocketKey]);

            if (count($this->userSockets["{$connection->app->id}:{$channel}:{$user->user_id}"]) === 0) {
                unset($this->userSockets["{$connection->app->id}:{$channel}:{$user->user_id}"]);
            }
        }

        return CWebSocket_Helper::createFulfilledPromise(true);
    }

    /**
     * Get the presence channel members.
     *
     * @param string|int $appId
     * @param string     $channel
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getChannelMembers($appId, $channel) {
        $members = $this->users["{$appId}:{$channel}"] ?? [];

        $members = c::collect($members)->map(function ($user) {
            return json_decode($user);
        })->unique('user_id')->toArray();

        return CWebSocket_Helper::createFulfilledPromise($members);
    }

    /**
     * Get a member from a presence channel based on connection.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $channel
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getChannelMember(ConnectionInterface $connection, $channel) {
        $member = $this->users["{$connection->app->id}:{$channel}"][$connection->socketId] ?? null;

        return CWebSocket_Helper::createFulfilledPromise($member);
    }

    /**
     * Get the presence channels total members count.
     *
     * @param string|int $appId
     * @param array      $channelNames
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getChannelsMembersCount($appId, array $channelNames) {
        $results = c::collect($channelNames)
            ->reduce(function ($results, $channel) use ($appId) {
                $results[$channel] = isset($this->users["{$appId}:{$channel}"])
                    ? count($this->users["{$appId}:{$channel}"])
                    : 0;

                return $results;
            }, []);

        return CWebSocket_Helper::createFulfilledPromise($results);
    }

    /**
     * Get the socket IDs for a presence channel member.
     *
     * @param string|int $userId
     * @param string|int $appId
     * @param string     $channelName
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getMemberSockets($userId, $appId, $channelName) {
        return CWebSocket_Helper::createFulfilledPromise(
            isset($this->userSockets["{$appId}:{$channelName}:{$userId}"]) ? $this->userSockets["{$appId}:{$channelName}:{$userId}"] : []
        );
    }

    /**
     * Keep tracking the connections availability when they pong.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return PromiseInterface[bool]
     */
    public function connectionPonged(ConnectionInterface $connection) {
        $connection->lastPongedAt = Carbon::now();

        return $this->updateConnectionInChannels($connection);
    }

    /**
     * Remove the obsolete connections that didn't ponged in a while.
     *
     * @return PromiseInterface[bool]
     */
    public function removeObsoleteConnections() {
        if (!$this->lock()->acquire()) {
            return CWebSocket_Helper::createFulfilledPromise(false);
        }

        $this->getLocalConnections()->then(function ($connections) {
            foreach ($connections as $connection) {
                $differenceInSeconds = $connection->lastPongedAt->diffInSeconds(Carbon::now());

                if ($differenceInSeconds > 120) {
                    $this->unsubscribeFromAllChannels($connection);
                }
            }
        });

        return CWebSocket_Helper::createFulfilledPromise(
            $this->lock()->forceRelease()
        );
    }

    /**
     * Update the connection in all channels.
     *
     * @param ConnectionInterface $connection
     *
     * @return PromiseInterface[bool]
     */
    public function updateConnectionInChannels($connection) {
        return $this->getLocalChannels($connection->app->id)
            ->then(function ($channels) use ($connection) {
                foreach ($channels as $channel) {
                    if ($channel->hasConnection($connection)) {
                        $channel->saveConnection($connection);
                    }
                }

                return true;
            });
    }

    /**
     * Mark the current instance as unable to accept new connections.
     *
     * @return $this
     */
    public function declineNewConnections() {
        $this->acceptsNewConnections = false;

        return $this;
    }

    /**
     * Check if the current server instance
     * accepts new connections.
     *
     * @return bool
     */
    public function acceptsNewConnections() {
        return $this->acceptsNewConnections;
    }

    /**
     * Get the channel class by the channel name.
     *
     * @param string $channelName
     *
     * @return string
     */
    protected function getChannelClassName($channelName) {
        if (cstr::startsWith($channelName, 'private-')) {
            return CWebSocket_Channel_PrivateChannel::class;
        }

        if (cstr::startsWith($channelName, 'presence-')) {
            return CWebSocket_Channel_PresenceChannel::class;
        }

        return CWebSocket_Channel::class;
    }

    /**
     * Get the unique identifier for the server.
     *
     * @return string
     */
    public function getServerId() {
        return $this->serverId;
    }

    /**
     * Get a new ArrayLock instance to avoid race conditions.
     *
     * @return \CCache_Lock_ArrayLock
     */
    protected function lock() {
        return new CCache_Lock_ArrayLock($this->store, static::$lockName, 0);
    }
}
