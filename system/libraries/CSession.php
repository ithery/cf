<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * mixed CSession_Store
 */
class CSession {

    use CTrait_Compat_Session;

    /**
     * Session singleton
     * 
     * @var CSession
     */
    protected static $instance;

    /**
     *
     * @var CSession_Store
     */
    protected $store;

    /**
     *
     * @var CSession_Driver
     */
    protected $driver;

    /**
     * 
     * Singleton instance of Session.
     *
     * @return CSession
     */
    public static function instance() {
        if (self::$instance == NULL) {
            // Create a new instance
            self::$instance = new CSession();
        }

        return self::$instance;
    }

    /**
     * On first session instance creation, sets up the driver and creates session.
     */
    private function __construct() {
        CF::log(CLogger::DEBUG, 'Session Library initialized');
    }

    /**
     * 
     * @return CSession_Store
     */
    public function store() {
        if ($this->store == null) {
            $this->store = CSession_Manager::instance()->createStore();
        }

        return $this->store;
    }

    /**
     * Get the session id.
     *
     * @return  string
     */
    public function id() {
        return $this->store()->getId();
    }

    public function __call($name, $arguments) {
        return call_user_func_array([$this->store(), $name], $arguments);
    }

    public static function manager() {
        return CSession_Manager::instance();
    }

}

// End Session Class
