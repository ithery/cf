<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDaemon_Factory {
    /**
     * @var CDaemon_Factory
     */
    protected static $instance;

    /**
     * @return CDaemon_Factory
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string $socketName
     * @param array  $contextOption
     *
     * @return \CDaemon_Worker_Listener_WebSocketListener
     */
    public static function createSocketListenerWorker($socketName = '', $contextOption = []) {
        $worker = new CDaemon_Worker_Listener_SocketListener();
        $worker->setSocket($socketName, $contextOption);

        return $worker;
    }
}
