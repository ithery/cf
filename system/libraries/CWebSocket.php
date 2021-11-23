<?php
use React\EventLoop\Factory as LoopFactory;

class CWebSocket {
    private static $channelManager;

    private static $loop;

    private static $httpLogger;

    private static $webSocketLogger;

    private static $connectionLogger;

    private static $statisticStore;

    private static $statisticCollector;

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
     * @return CWebSocket_Server_Logger_HttpLogger
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
            $class = CF::config('websocket.store');
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
}
