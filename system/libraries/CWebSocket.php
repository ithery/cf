<?php
use React\EventLoop\LoopInterface;

/**
 * Websocket implementation.
 *
 * @see https://github.com/beyondcode/laravel-websockets
 */
class CWebSocket {
    /**
     * @var CWebSocket_Contract_ChannelManagerInterface
     */
    private static $channelManager;

    /**
     * @var CWebSocket_Server_Logger_HttpLogger
     */
    private static $httpLogger;

    /**
     * @var CWebSocket_Server_Logger_WebSocketLogger
     */
    private static $webSocketLogger;

    /**
     * @var CWebSocket_Server_Logger_ConnectionLogger
     */
    private static $connectionLogger;

    /**
     * @var CWebSocket_Contract_StatisticStoreInterface
     */
    private static $statisticStore;

    /**
     * @var CWebSocket_Contract_StatisticCollectorInterface
     */
    private static $statisticCollector;

    /**
     * @return CWebSocket_AppManager
     */
    public static function appManager() {
        return CWebSocket_AppManager::instance();
    }

    /**
     * @return CWebSocket_ChannelManager_LocalChannelManager
     */
    public static function channelManager() {
        return static::$channelManager;
    }

    public static function setChannelManager($channelManager) {
        static::$channelManager = $channelManager;
    }

    /**
     * @return CWebSocket_Server_Logger_HttpLogger
     */
    public static function httpLogger() {
        return static::$httpLogger;
    }

    public static function setHttpLogger($logger) {
        static::$httpLogger = $logger;
    }

    /**
     * @return CWebSocket_Server_Logger_WebSocketLogger
     */
    public static function webSocketLogger() {
        return static::$webSocketLogger;
    }

    public static function setWebSocketLogger($logger) {
        static::$webSocketLogger = $logger;
    }

    /**
     * @return CWebSocket_Server_Logger_ConnectionLogger
     */
    public static function connectionLogger() {
        return static::$connectionLogger;
    }

    public static function setConnectionLogger($logger) {
        static::$connectionLogger = $logger;
    }

    /**
     * @return CWebSocket_Router
     */
    public static function router() {
        return CWebSocket_Router::instance();
    }

    /**
     * @return CWebSocket_Statistic_Store_DatabaseStore
     */
    public static function statisticStore() {
        if (static::$statisticStore == null) {
            $class = CF::config('websocket.statistics.store', CWebSocket_Statistic_Store_DatabaseStore::class);
            static::$statisticStore = new $class();
        }

        return static::$statisticStore;
    }

    public static function statisticCollector() {
        if (static::$statisticCollector == null) {
            $replicationMode = CF::config('websocket.replication.mode', 'local');
            $class = CF::config('websocket.replication.modes.' . $replicationMode . '.collector');

            static::$statisticCollector = new $class();
        }

        return static::$statisticCollector;
    }

    public static function client(LoopInterface $loop = null) {
        return new CWebSocket_Client($loop);
    }
}
