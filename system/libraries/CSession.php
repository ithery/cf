<?php

defined('SYSPATH') or die('No direct access allowed.');

class CSession {
    // Session singleton
    protected static $instance;
    // Protected key names (cannot be set by the user)
    protected static $protect = ['session_id', 'user_agent', 'last_activity', 'ip_address', 'total_hits', '_kf_flash_'];
    // Configuration and driver
    protected static $config;
    protected static $driver;
    // Flash variables
    protected static $flash;
    // Input library
    protected $input;

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
    public function __construct() {
        $this->input = Input::instance();

        // This part only needs to be run once
        if (CSession::$instance === null) {
            // Load config
            CSession::$config = CF::config('session');

            // Makes a mirrored array, eg: foo=foo
            CSession::$protect = array_combine(CSession::$protect, CSession::$protect);

            if (session_status() == PHP_SESSION_NONE) {
                // Configure garbage collection
                ini_set('session.gc_probability', (int) CSession::$config['gc_probability']);
                ini_set('session.gc_divisor', 100);
                ini_set('session.gc_maxlifetime', (CSession::$config['expiration'] == 0) ? 86400 : CSession::$config['expiration']);
            }
            // Create a new session
            $this->create();

            if (CSession::$config['regenerate'] > 0 and ($_SESSION['total_hits'] % CSession::$config['regenerate']) === 0) {
                // Regenerate session id and update session cookie
                $this->regenerate();
            } else {
                // Always update session cookie to keep the session alive
                cookie::set(CSession::$config['name'], $_SESSION['session_id'], CSession::$config['expiration']);
            }

            // Close the session just before sending the headers, so that
            // the session cookie(s) can be written.
            CFEvent::add('system.send_headers', [$this, 'write_close']);

            // Make sure that sessions are closed before exiting
            register_shutdown_function([$this, 'write_close']);

            // Singleton instance
            CSession::$instance = $this;
        }

        CF::log(CLogger::DEBUG, 'Session Library initialized');
    }

    /**
     * Get the session id.
     *
     * @return string
     */
    public function id() {
        return $_SESSION['session_id'];
    }

    /**
     * Create a new session.
     *
     * @param   array  variables to set after creation
     * @param null|mixed $vars
     *
     * @return void
     */
    public function create($vars = null) {
        // Destroy any current sessions
        $this->destroy();

        if (CSession::$config['driver'] !== 'native') {
            // Set driver name

            $driverName = CSession::$config['driver'];
            if (!preg_match('/^[A-Z]/', $driverName)) {
                $driverName = ucfirst($driverName);
            }

            $driver = 'CSession_Driver_' . $driverName;

            $cacheBasedDriver = ['Redis'];

            if (!in_array($driverName, $cacheBasedDriver)) {
                // Load the driver
                try {
                    // Validation of the driver
                    $class = new ReflectionClass($driver);
                    // Initialize the driver
                    CSession::$driver = $class->newInstance();
                } catch (ReflectionException $ex) {
                    throw new CException('The :driver driver for the :class library could not be found', [':driver' => CSession::$config['driver'], ':class' => get_class($this)]);
                }
            } else {
                $method = 'create' . $driverName . 'Driver';
                CSession::$driver = call_user_func([CSession_Factory::class, $method]);
            }

            // Validate the driver
            if (!(CSession::$driver instanceof CSession_Driver)) {
                throw new CFException('The :driver driver for the :class library must implement the :interface interface', [':driver' => CSession::$config['driver'], ':class' => get_class($this), ':interface' => 'Session_Driver']);
            }

            // Register non-native driver as the session handler
            session_set_save_handler([CSession::$driver, 'open'], [CSession::$driver, 'close'], [CSession::$driver, 'read'], [CSession::$driver, 'write'], [CSession::$driver, 'destroy'], [CSession::$driver, 'gc']);
        }

        // Validate the session name
        if (!preg_match('~^(?=.*[a-z])[a-z0-9_]++$~iD', CSession::$config['name'])) {
            throw new CFException('The session_name, :name, is invalid. It must contain only alphanumeric characters and underscores. Also at least one letter must be present.', [':name', CSession::$config['name']]);
        }

        // Name the session, this will also be the name of the cookie
        session_name(CSession::$config['name']);

        // Set the session cookie parameters
        session_set_cookie_params(CSession::$config['expiration'], CF::config('cookie.path'), CF::config('cookie.domain'), CF::config('cookie.secure'), CF::config('cookie.httponly'));

        // Start the session!
        if (session_status() == PHP_SESSION_NONE) {
            @session_start();
        }

        // Put session_id in the session variable
        $_SESSION['session_id'] = session_id();

        // Set defaults
        if (!isset($_SESSION['_kf_flash_'])) {
            $_SESSION['total_hits'] = 0;
            $_SESSION['_kf_flash_'] = [];

            $_SESSION['user_agent'] = CF::userAgent();
            $_SESSION['ip_address'] = $this->input->ip_address();
        }

        // Set up flash variables
        CSession::$flash = &$_SESSION['_kf_flash_'];

        // Increase total hits
        $_SESSION['total_hits'] += 1;

        // Validate data only on hits after one
        if ($_SESSION['total_hits'] > 1) {
            // Validate the session
            foreach (CSession::$config['validate'] as $valid) {
                switch ($valid) {
                    // Check user agent for consistency
                    case 'user_agent':
                        if ($_SESSION[$valid] !== CF::userAgent()) {
                            return $this->create();
                        }
                        break;

                    // Check ip address for consistency
                    case 'ip_address':
                        if ($_SESSION[$valid] !== $this->input->$valid()) {
                            return $this->create();
                        }
                        break;

                    // Check expiration time to prevent users from manually modifying it
                    case 'expiration':
                        if (time() - $_SESSION['last_activity'] > ini_get('session.gc_maxlifetime')) {
                            return $this->create();
                        }
                        break;
                }
            }
        }

        // Expire flash keys
        $this->expire_flash();

        // Update last activity
        $_SESSION['last_activity'] = time();

        // Set the new data
        CSession::set($vars);
    }

    /**
     * Regenerates the global session id.
     *
     * @return void
     */
    public function regenerate() {
        if (CSession::$config['driver'] === 'native') {
            // Generate a new session id
            // Note: also sets a new session cookie with the updated id
            session_regenerate_id(true);

            // Update session with new id
            $_SESSION['session_id'] = session_id();
        } else {
            // Pass the regenerating off to the driver in case it wants to do anything special
            $_SESSION['session_id'] = CSession::$driver->regenerate();
        }

        // Get the session name
        $name = session_name();

        if (isset($_COOKIE[$name])) {
            // Change the cookie value to match the new session id to prevent "lag"
            $_COOKIE[$name] = $_SESSION['session_id'];
        }
    }

    /**
     * Destroys the current session.
     *
     * @return void
     */
    public function destroy() {
        if (session_id() !== '') {
            // Get the session name
            $name = session_name();

            // Destroy the session
            session_destroy();

            // Re-initialize the array
            $_SESSION = [];

            // Delete the session cookie
            cookie::delete($name);
        }
    }

    /**
     * Runs the system.session_write event, then calls session_write_close.
     *
     * @return void
     */
    public function write_close() {
        static $run;

        if ($run === null) {
            $run = true;

            // Run the events that depend on the session being open
            CFEvent::run('system.session_write');

            // Expire flash keys
            $this->expire_flash();

            // Close the session
            session_write_close();
        }
    }

    /**
     * Set a session variable.
     *
     * @param   string|array  key, or array of values
     * @param   mixed         value (if keys is not an array)
     * @param mixed $keys
     * @param mixed $val
     *
     * @return void
     */
    public function set($keys, $val = false) {
        if (empty($keys)) {
            return false;
        }

        if (!is_array($keys)) {
            $keys = [$keys => $val];
        }

        foreach ($keys as $key => $val) {
            if (isset(CSession::$protect[$key])) {
                continue;
            }

            // Set the key
            $_SESSION[$key] = $val;
        }
    }

    /**
     * Set a flash variable.
     *
     * @param   string|array  key, or array of values
     * @param   mixed         value (if keys is not an array)
     * @param mixed $keys
     * @param mixed $val
     *
     * @return void
     */
    public function set_flash($keys, $val = false) {
        if (empty($keys)) {
            return false;
        }

        if (!is_array($keys)) {
            $keys = [$keys => $val];
        }

        foreach ($keys as $key => $val) {
            if ($key == false) {
                continue;
            }

            CSession::$flash[$key] = 'new';
            CSession::set($key, $val);
        }
    }

    /**
     * Freshen one, multiple or all flash variables.
     *
     * @param   string  variable key(s)
     * @param null|mixed $keys
     *
     * @return void
     */
    public function keep_flash($keys = null) {
        $keys = ($keys === null) ? array_keys(CSession::$flash) : func_get_args();

        foreach ($keys as $key) {
            if (isset(CSession::$flash[$key])) {
                CSession::$flash[$key] = 'new';
            }
        }
    }

    /**
     * Expires old flash data and removes it from the session.
     *
     * @return void
     */
    public function expire_flash() {
        static $run;

        // Method can only be run once
        if ($run === true) {
            return;
        }

        if (!empty(CSession::$flash)) {
            foreach (CSession::$flash as $key => $state) {
                if ($state === 'old') {
                    // Flash has expired
                    unset(CSession::$flash[$key], $_SESSION[$key]);
                } else {
                    // Flash will expire
                    CSession::$flash[$key] = 'old';
                }
            }
        }

        // Method has been run
        $run = true;
    }

    /**
     * Get a variable. Access to sub-arrays is supported with key.subkey.
     *
     * @param   string  variable key
     * @param   mixed   default value returned if variable does not exist
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed Variable data if key specified, otherwise array containing all session data.
     */
    public function get($key = false, $default = false) {
        if (empty($key)) {
            return $_SESSION;
        }

        return carr::get($_SESSION, $key, $default);
    }

    /**
     * Get a variable, and delete it.
     *
     * @param   string  variable key
     * @param   mixed   default value returned if variable does not exist
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOnce($key, $default = false) {
        $return = $this->get($key, $default);
        $this->delete($key);

        return $return;
    }

    /**
     * Delete one or more variables.
     *
     * @param   string  variable key(s)
     * @param mixed $keys
     *
     * @return void
     */
    public function delete($keys) {
        $args = func_get_args();

        foreach ($args as $key) {
            $this->forget($key);
        }
    }

    public function forget($key) {
        if (isset(CSession::$protect[$key])) {
            return false;
        }
        // Unset the key
        unset($_SESSION[$key]);
    }

    public function store() {
        return $this;
    }
}

// End Session Class
