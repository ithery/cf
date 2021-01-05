<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Session Class
 *
 * @see CSession_Store
 *
 * @method void set(string $key, mixed|null $value = null)
 */
class CSession {
    use CTrait_Compat_Session;

    protected $initialized = false;

    /**
     * Session singleton
     *
     * @var CSession
     */
    protected static $instance;

    /**
     * @var CSession_Store
     */
    protected $store;

    /**
     * @var CSession_Driver
     */
    protected $driver;

    /**
     * Singleton instance of Session.
     *
     * @return CSession
     */
    public static function instance() {
        if (self::$instance == null) {
            // Create a new instance
            self::$instance = new CSession();
        }

        return self::$instance;
    }

    /**
     * On first session instance creation, sets up the driver and creates session.
     */
    private function __construct() {
        $this->initializeSession();
        CF::log(CLogger::DEBUG, 'Session Library initialized');
    }

    /**
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
     * @return string
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

    protected function initializeSession() {
        if (!$this->initialized && static::sessionConfigured()) {
            $request = CHTTP::request();
            static::manager()->applyNativeSession();

            return c::tap($this->store(), function ($session) use ($request) {
                $session->setId($request->cookies->get($session->getName()));
                $session->setRequestOnHandler($request);
                $session->start();
            });
            $this->initialized = true;
        }
    }

    /**
     * Determine if a session driver has been configured.
     *
     * @return bool
     */
    public static function sessionConfigured() {
        return !is_null(carr::get(static::manager()->getSessionConfig(), 'driver'));
    }
}

// End Session Class
