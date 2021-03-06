<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 16, 2019, 3:36:29 AM
 */
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
