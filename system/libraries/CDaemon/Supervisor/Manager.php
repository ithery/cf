<?php
/**
 * @see CDaemon_Supervisor
 * @see CDaemon_Supervisor_Bootstrap
 */
class CDaemon_Supervisor_Manager {
    private static $instance;

    /**
     * @return CDaemon_Supervisor_Manager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function __construct() {
    }
}
