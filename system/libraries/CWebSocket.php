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

    public static function channelManager() {
        if (static::$channelManager == null) {
            $mode = CF::config('websocket.replication.mode', 'local');
            $class = CF::config('websocket.replication.modes,' . $mode . '.channel_manager');
            static::$channelManager = new $class(static::loop());
        }

        return static::$channelManager;
    }

    public static function setChannelManager($channelManager) {
        static::$channelManager = $channelManager;
    }

    public static function loop() {
        if (static::$loop == null) {
            static::$loop = LoopFactory::create();
        }

        return static::$loop;
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

    public static function router() {
        return CWebSocket::router();
    }

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
