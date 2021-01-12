<?php

defined('SYSPATH') or die('No direct access allowed.');

class CSession {
    use CTrait_Compat_Session;

    // Session singleton
    protected static $instance;
    // Protected key names (cannot be set by the user)
    protected static $protect = ['session_id', 'user_agent', 'last_activity', 'ip_address', 'total_hits', '_kf_flash_'];
    // Configuration and driver
    protected static $config;
    protected static $driver;

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
            CFEvent::add('system.send_headers', [$this, 'writeClose']);

            // Make sure that sessions are closed before exiting
            register_shutdown_function([$this, 'writeClose']);

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
     * @param null|array $vars variables to set after creation
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
                CSession::$driver = call_user_func([CSession_Factory::instance(), $method]);
            }

            // Validate the driver
            if (!(CSession::$driver instanceof CSession_Driver)) {
                throw new CException('The :driver driver for the :class library must implement the :interface interface', [':driver' => CSession::$config['driver'], ':class' => get_class($this), ':interface' => 'Session_Driver']);
            }

            // Register non-native driver as the session handler
            session_set_save_handler([CSession::$driver, 'open'], [CSession::$driver, 'close'], [CSession::$driver, 'read'], [CSession::$driver, 'write'], [CSession::$driver, 'destroy'], [CSession::$driver, 'gc']);
        }

        // Validate the session name
        if (!preg_match('~^(?=.*[a-z])[a-z0-9_]++$~iD', CSession::$config['name'])) {
            throw new CException('The session_name, :name, is invalid. It must contain only alphanumeric characters and underscores. Also at least one letter must be present.', [':name', CSession::$config['name']]);
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

            $_SESSION['user_agent'] = c::userAgent();
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
                        if ($_SESSION[$valid] !== c::userAgent()) {
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
        $this->set($vars);

        if (!$this->has('_token')) {
            $this->regenerateToken();
        }
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
    public function writeClose() {
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
     * @param string|array $keys key, or array of values
     * @param mixed        $val  value (if keys is not an array)
     *
     * @return void
     */
    public static function set($keys, $val = false) {
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
     * Get a variable. Access to sub-arrays is supported with key.subkey.
     *
     * @param string $key     variable key
     * @param mixed  $default default value returned if variable does not exist
     *
     * @return mixed Variable data if key specified, otherwise array containing all session data.
     */
    public function get($key = false, $default = null) {
        if (empty($key)) {
            return $_SESSION;
        }

        return carr::get($_SESSION, $key, $default);
    }

    /**
     * Get a variable, and delete it.
     *
     * @param string $key     variable key
     * @param mixed  $default default value returned if variable does not exist
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
     * @param string $keys variable key(s)
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

    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token() {
        return $this->get('_token');
    }

    /**
     * Regenerate the CSRF token value.
     *
     * @return void
     */
    public function regenerateToken() {
        $this->set('_token', cstr::random(40));
    }

    /**
     * Checks if a key is present and not null.
     *
     * @param string|array $key
     *
     * @return bool
     */
    public function has($key) {
        return !c::collect(is_array($key) ? $key : func_get_args())->contains(function ($key) {
            return is_null($this->get($key));
        });
    }

    /**
     * Flash an input array to the session.
     *
     * @param array $value
     *
     * @return void
     */
    public function flashInput(array $value) {
        $this->flash('_old_input', $value);
    }

    /**
     * Flash a key / value pair to the session.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function flash($key, $value = true) {
        $this->put($key, $value);

        $this->push('_flash.new', $key);

        $this->removeFromOldFlashData([$key]);
    }

    /**
     * Remove the given keys from the old flash data.
     *
     * @param array $keys
     *
     * @return void
     */
    protected function removeFromOldFlashData(array $keys) {
        $this->put('_flash.old', array_diff($this->get('_flash.old', []), $keys));
    }

    /**
     * Put a key / value pair or array of key / value pairs in the session.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return void
     */
    public function put($key, $value = null) {
        if (!is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            $this->set($arrayKey, $arrayValue);
        }
    }

    /**
     * Push a value onto a session array.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function push($key, $value) {
        $array = $this->get($key, []);

        $array[] = $value;

        $this->put($key, $array);
    }

    /**
     * Generate a new session ID for the session.
     *
     * @param bool $destroy
     *
     * @return bool
     */
    public function migrate($destroy = false) {
        return true;
    }

    /**
     * Get the value of a given key and then forget it.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function pull($key, $default = null) {
        return carr::pull($_SESSION, $key, $default);
    }

    /**
     * Remove an item from the session, returning its value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function remove($key) {
        return $this->pull($key);
    }
}

// End Session Class
