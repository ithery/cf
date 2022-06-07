<?php

use React\Promise\PromiseInterface;

class CWebSocket_Statistic_Collector_RedisCollector extends CWebSocket_Statistic_Collector_MemoryCollector {
    /**
     * The Redis manager instance.
     *
     * @var \CRedis
     */
    protected $redis;

    /**
     * The set name for the Redis storage.
     *
     * @var string
     */
    protected static $redisSetName = 'cf-websocket:apps';

    /**
     * The lock name to use on Redis to avoid multiple
     * collector-to-store actions that may result
     * in multiple data points set to the store.
     *
     * @var string
     */
    protected static $redisLockName = 'cf-websocket:collector:lock';

    /**
     * Initialize the logger.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->redis = CRedis::instance()->connection(
            CF::config('websocket.replication.modes.redis.connection', 'default')
        );
    }

    /**
     * Handle the incoming websocket message.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function webSocketMessage($appId) {
        $this->ensureAppIsInSet($appId)
            ->hincrby($this->channelManager->getStatsRedisHash($appId, null), 'websocket_message_count', 1);
    }

    /**
     * Handle the incoming API message.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function apiMessage($appId) {
        $this->ensureAppIsInSet($appId)
            ->hincrby($this->channelManager->getStatsRedisHash($appId, null), 'api_message_count', 1);
    }

    /**
     * Handle the new conection.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function connection($appId) {
        // Increment the current connections count by 1.
        $this->ensureAppIsInSet($appId)
            ->hincrby(
                $this->channelManager->getStatsRedisHash($appId, null),
                'current_connection_count',
                1
            )
            ->then(function ($currentConnectionsCount) use ($appId) {
                // Get the peak connections count from Redis.
                $this->channelManager
                    ->getPublishClient()
                    ->hget(
                        $this->channelManager->getStatsRedisHash($appId, null),
                        'peak_connection_count'
                    )
                    ->then(function ($currentPeakConnectionCount) use ($currentConnectionsCount, $appId) {
                        // Extract the greatest number between the current peak connection count
                        // and the current connection number.
                        $peakConnectionsCount = is_null($currentPeakConnectionCount)
                            ? $currentConnectionsCount
                            : max($currentPeakConnectionCount, $currentConnectionsCount);

                        // Then set it to the database.
                        $this->channelManager
                            ->getPublishClient()
                            ->hset(
                                $this->channelManager->getStatsRedisHash($appId, null),
                                'peak_connection_count',
                                $peakConnectionsCount
                            );
                    });
            });
    }

    /**
     * Handle disconnections.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function disconnection($appId) {
        // Decrement the current connections count by 1.
        $this->ensureAppIsInSet($appId)
            ->hincrby($this->channelManager->getStatsRedisHash($appId, null), 'current_connection_count', -1)
            ->then(function ($currentConnectionsCount) use ($appId) {
                // Get the peak connections count from Redis.
                $this->channelManager
                    ->getPublishClient()
                    ->hget($this->channelManager->getStatsRedisHash($appId, null), 'peak_connection_count')
                    ->then(function ($currentPeakConnectionCount) use ($currentConnectionsCount, $appId) {
                        // Extract the greatest number between the current peak connection count
                        // and the current connection number.
                        $peakConnectionsCount = is_null($currentPeakConnectionCount)
                            ? $currentConnectionsCount
                            : max($currentPeakConnectionCount, $currentConnectionsCount);

                        // Then set it to the database.
                        $this->channelManager
                            ->getPublishClient()
                            ->hset(
                                $this->channelManager->getStatsRedisHash($appId, null),
                                'peak_connection_count',
                                $peakConnectionsCount
                            );
                    });
            });
    }

    /**
     * Save all the stored statistics.
     *
     * @return void
     */
    public function save() {
        $this->lock()->get(function () {
            $this->channelManager
                ->getPublishClient()
                ->smembers(static::$redisSetName)
                ->then(function ($members) {
                    foreach ($members as $appId) {
                        $this->channelManager
                            ->getPublishClient()
                            ->hgetall($this->channelManager->getStatsRedisHash($appId, null))
                            ->then(function ($list) use ($appId) {
                                if (!$list) {
                                    return;
                                }

                                $statistic = $this->arrayToStatisticInstance(
                                    $appId,
                                    CWebSocket_Helper::redisListToArray($list)
                                );

                                if ($statistic->shouldHaveTracesRemoved()) {
                                    return $this->resetAppTraces($appId);
                                }

                                $this->createRecord($statistic, $appId);

                                $this->channelManager
                                    ->getGlobalConnectionsCount($appId)
                                    ->then(function ($currentConnectionsCount) use ($appId) {
                                        $currentConnectionsCount === 0 || is_null($currentConnectionsCount)
                                            ? $this->resetAppTraces($appId)
                                            : $this->resetStatistics($appId, $currentConnectionsCount);
                                    });
                            });
                    }
                });
        });
    }

    /**
     * Flush the stored statistics.
     *
     * @return void
     */
    public function flush() {
        $this->getStatistics()->then(function ($statistics) {
            foreach ($statistics as $appId => $statistic) {
                $this->resetAppTraces($appId);
            }
        });
    }

    /**
     * Get the saved statistics.
     *
     * @return PromiseInterface[array]
     */
    public function getStatistics() {
        return $this->channelManager
            ->getPublishClient()
            ->smembers(static::$redisSetName)
            ->then(function ($members) {
                $appsWithStatistics = [];

                foreach ($members as $appId) {
                    $this->channelManager
                        ->getPublishClient()
                        ->hgetall($this->channelManager->getStatsRedisHash($appId, null))
                        ->then(function ($list) use ($appId, &$appsWithStatistics) {
                            $appsWithStatistics[$appId] = $this->arrayToStatisticInstance(
                                $appId,
                                CWebSocket_Helper::redisListToArray($list)
                            );
                        });
                }

                return $appsWithStatistics;
            });
    }

    /**
     * Get the saved statistics for an app.
     *
     * @param string|int $appId
     *
     * @return PromiseInterface[\BeyondCode\LaravelWebSockets\Statistics\Statistic|null]
     */
    public function getAppStatistics($appId) {
        return $this->channelManager
            ->getPublishClient()
            ->hgetall($this->channelManager->getStatsRedisHash($appId, null))
            ->then(function ($list) use ($appId) {
                return $this->arrayToStatisticInstance(
                    $appId,
                    CWebSocket_Helper::redisListToArray($list)
                );
            });
    }

    /**
     * Reset the statistics to a specific connection count.
     *
     * @param string|int $appId
     * @param int        $currentConnectionCount
     *
     * @return void
     */
    public function resetStatistics($appId, int $currentConnectionCount) {
        $this->channelManager
            ->getPublishClient()
            ->hset(
                $this->channelManager->getStatsRedisHash($appId, null),
                'current_connection_count',
                $currentConnectionCount
            );

        $this->channelManager
            ->getPublishClient()
            ->hset(
                $this->channelManager->getStatsRedisHash($appId, null),
                'peak_connection_count',
                max(0, $currentConnectionCount)
            );

        $this->channelManager
            ->getPublishClient()
            ->hset(
                $this->channelManager->getStatsRedisHash($appId, null),
                'websocket_message_count',
                0
            );

        $this->channelManager
            ->getPublishClient()
            ->hset(
                $this->channelManager->getStatsRedisHash($appId, null),
                'api_message_count',
                0
            );
    }

    /**
     * Remove all app traces from the database if no connections have been set
     * in the meanwhile since last save.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function resetAppTraces($appId) {
        parent::resetAppTraces($appId);

        $this->channelManager
            ->getPublishClient()
            ->hdel(
                $this->channelManager->getStatsRedisHash($appId, null),
                'current_connection_count'
            );

        $this->channelManager
            ->getPublishClient()
            ->hdel(
                $this->channelManager->getStatsRedisHash($appId, null),
                'peak_connection_count'
            );

        $this->channelManager
            ->getPublishClient()
            ->hdel(
                $this->channelManager->getStatsRedisHash($appId, null),
                'websocket_message_count'
            );

        $this->channelManager
            ->getPublishClient()
            ->hdel(
                $this->channelManager->getStatsRedisHash($appId, null),
                'api_message_count'
            );

        $this->channelManager
            ->getPublishClient()
            ->srem(static::$redisSetName, $appId);
    }

    /**
     * Ensure the app id is stored in the Redis database.
     *
     * @param string|int $appId
     *
     * @return \Clue\React\Redis\Client
     */
    protected function ensureAppIsInSet($appId) {
        $this->channelManager
            ->getPublishClient()
            ->sadd(static::$redisSetName, $appId);

        return $this->channelManager->getPublishClient();
    }

    /**
     * Get a new RedisLock instance to avoid race conditions.
     *
     * @return \CCache_LockAbstract
     */
    protected function lock() {
        return new CCache_Lock_RedisLock($this->redis, static::$redisLockName, 0);
    }

    /**
     * Transform a key-value pair to a Statistic instance.
     *
     * @param string|int $appId
     * @param array      $stats
     *
     * @return \CWebSocket_Statistic
     */
    protected function arrayToStatisticInstance($appId, array $stats) {
        return CWebSocket_Statistic::createNew($appId)
            ->setCurrentConnectionsCount(isset($stats['current_connection_count']) ? $stats['current_connection_count'] : 0)
            ->setPeakConnectionsCount(isset($stats['peak_connection_count']) ? $stats['peak_connection_count'] : 0)
            ->setWebSocketMessagesCount(isset($stats['websocket_message_count']) ? $stats['websocket_message_count'] : 0)
            ->setApiMessagesCount(isset($stats['api_message_count']) ? $stats['api_message_count'] : 0);
    }
}
