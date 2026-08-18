<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDaemon_Server {
    /**
     * @var null|CDaemon_Server_EventLoop
     */
    protected static $eventLoop = null;

    /**
     * @var bool
     */
    protected static $gracefulStop = false;

    /**
     * @return CDaemon_Server_EventLoop
     */
    public static function getEventLoop() {
        if (static::$eventLoop === null) {
            static::$eventLoop = new CDaemon_Server_EventLoop();
        }

        return static::$eventLoop;
    }

    /**
     * @param CDaemon_Server_EventLoop $eventLoop
     *
     * @return void
     */
    public static function setEventLoop(CDaemon_Server_EventLoop $eventLoop) {
        static::$eventLoop = $eventLoop;
    }

    /**
     * @param string $msg
     *
     * @return void
     */
    public static function log($msg) {
        if ($msg instanceof Exception || $msg instanceof Error) {
            $msg = $msg->getMessage() . "\n" . $msg->getTraceAsString();
        }
        CDaemon::log($msg);
    }

    /**
     * @param string $msg
     *
     * @return void
     */
    public static function safeEcho($msg) {
        set_error_handler(function () {
        });
        if (defined('STDOUT') && is_resource(STDOUT)) {
            fwrite(STDOUT, $msg);
        }
        restore_error_handler();
    }

    /**
     * @return bool
     */
    public static function getGracefulStop() {
        return static::$gracefulStop;
    }

    /**
     * @param int    $code
     * @param string $log
     *
     * @return void
     */
    public static function stopAll($code = 0, $log = '') {
        static::$gracefulStop = true;
        if ($log) {
            static::log($log);
        }
    }
}
