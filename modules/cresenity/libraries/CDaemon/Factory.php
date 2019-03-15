<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 3:36:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDaemon_Factory {

    /**
     *
     * @var CDaemon_Factory 
     */
    protected static $instance;

    /**
     * 
     * @return CDaemon_Factory
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function createWebSocketListenerWorker() {
        return new CDaemon_Worker_Listener_WebSocketListener();
    }

}
