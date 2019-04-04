<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CF {

    // Security check that is added to all generated PHP files
    const FILE_SECURITY = '<?php defined(\'SYSPATH\') OR die(\'No direct script access.\');';

    // The singleton instance of the controller
    public static $instance;
    // Output buffering level
    private static $buffer_level;
    // Will be set to TRUE when an exception is caught
    public static $has_error = FALSE;
    // The final output that will displayed by C
    public static $output = '';
    // The current user agent
    public static $user_agent;
    // The current locale
    public static $locale;
    // Configuration
    private static $configuration;
    // Include paths
    private static $paths;
    // Logged messages
    private static $log;
    // Cache lifetime
    private static $cache_lifetime;
    // Log levels
    private static $log_levels = array(
        'error' => 1,
        'alert' => 2,
        'info' => 3,
        'debug' => 4,
    );
    public static $charset = 'utf-8';

    /* log threshold default , CLogger::LOG_WARNING (4) */
    public static $log_threshold = 4;
    public static $global_xss_filtering = TRUE;
    // Internal caches and write status
    private static $internal_cache = array();
    private static $write_cache;
    private static $internal_cache_path;
    private static $internal_cache_key;
    private static $internal_cache_encrypt;
    private static $data;
    private static $sharedAppCode = array();
    public static $instances;

    /**
     * @var  CLogger  logging object
     */
    public static $logger;

    /**
     * @var  CConfig  config object
     */
    public static $config;

    public static function domain_data($domain) {
        $data = CFData::get($domain, 'domain');
        $result = array();
        $result['app_id'] = '';
        $result['app_code'] = '';
        $result['org_id'] = '';
        $result['org_code'] = '';
        $result['store_id'] = '';
        $result['store_code'] = '';
        $result['shared_app_code'] = array();
        $result['theme'] = '';

        if ($data != null) {
            $result['app_id'] = isset($data['app_id']) ? $data['app_id'] : null;
            $result['app_code'] = isset($data['app_code']) ? $data['app_code'] : null;
            $result['org_id'] = isset($data['org_id']) ? $data['org_id'] : null;
            $result['org_code'] = isset($data['org_code']) ? $data['org_code'] : null;
            $result['store_id'] = isset($data['store_id']) ? $data['store_id'] : null;
            $result['store_code'] = isset($data['store_code']) ? $data['store_code'] : null;
            $result['shared_app_code'] = isset($data['shared_app_code']) ? $data['shared_app_code'] : array();
            $result['theme'] = isset($data['theme']) ? $data['theme'] : null;
        }
        return $result;
    }

    /**
     * Sets up the PHP environment. Adds error/exception handling, output
     * buffering, and adds an auto-loading method for loading classes.
     *
     * This method is run immediately when this file is loaded, and is
     * benchmarked as environment_setup.
     *
     * For security, this function also destroys the $_REQUEST global variable.
     * Using the proper global (GET, POST, COOKIE, etc) is inherently more secure.
     * The recommended way to fetch a global variable is using the Input library.
     * @see http://www.php.net/globals
     *
     * @return  void
     */
    public static function setup() {
        static $run;

        // This function can only be run once
        if ($run === TRUE)
            return;

        // Start the environment setup benchmark
        CFBenchmark::start(SYSTEM_BENCHMARK . '_environment_setup');


        $capppath = APPPATH;
        $defaultpath = APPPATH;
        if (strlen(self::appCode()) > 0) {
            $capppath .= self::appCode() . DS;
            $defaultpath .= self::appCode() . DS;
        }
        if (strlen(self::orgCode()) > 0) {
            $capppath .= self::orgCode() . DS;
        }


        if (is_dir($defaultpath . "default" . DS)) {
            $defaultpath .= "default" . DS;
        }
        if (is_dir($capppath . "default" . DS)) {
            $capppath .= "default" . DS;
        }

        define('CAPPPATH', $capppath);
        define('DEFAULTPATH', $defaultpath);

        // Define CF error constant
        define('E_CF', 42);

        // Define 404 error constant
        define('E_PAGE_NOT_FOUND', 43);

        // Define database error constant
        define('E_DATABASE_ERROR', 44);


        // Start output buffering
        ob_start(array(__CLASS__, 'output_buffer'));

        // Save buffering level
        self::$buffer_level = ob_get_level();

        // Set autoloader
        spl_autoload_register(array('CF', 'auto_load'));

        // Set error handler
        set_error_handler(array('CF', 'exception_handler'));

        // Set exception handler
        set_exception_handler(array('CF', 'exception_handler'));

        // Set and test the logger instance, we need to know whats wrong when CF Fail
        self::$logger = CLogger::instance();

        // Set and test the config, we need config can loaded normally to run CF
        self::$config = CConfig::instance('app');


        // Disable notices and "strict" errors
        $ER = error_reporting(~E_NOTICE & ~E_STRICT);

        // Set the user agent
        self::$user_agent = (!empty($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '');

        if (function_exists('date_default_timezone_set')) {
            $timezone = self::config('locale.timezone');

            // Set default timezone, due to increased validation of date settings
            // which cause massive amounts of E_NOTICEs to be generated in PHP 5.2+
            date_default_timezone_set(empty($timezone) ? date_default_timezone_get() : $timezone);
        }

        // Restore error reporting
        error_reporting($ER);

        // Send default text/html UTF-8 header
        header('Content-Type: text/html; charset=UTF-8');

        // Load locales
        $locales = self::config('locale.language');

        // Make first locale UTF-8
        $locales[0] .= '.UTF-8';

        // Set locale information
        self::$locale = setlocale(LC_ALL, $locales);

        if (self::$configuration['core']['log_threshold'] > 0) {
            // Set the log directory
            self::log_directory(self::$configuration['core']['log_directory']);

            // Enable log writing at shutdown
            register_shutdown_function(array(__CLASS__, 'log_save'));
        }

        // Enable CF routing
        CFEvent::add('system.routing', array('CFRouter', 'find_uri'));
        CFEvent::add('system.routing', array('CFRouter', 'setup'));

        // Enable CF controller initialization
        CFEvent::add('system.execute', array('CF', 'instance'));

        // Enable CF 404 pages
        CFEvent::add('system.404', array('CF', 'show_404'));

        // Enable CF output handling
        CFEvent::add('system.shutdown', array('CF', 'shutdown'));

        CFBenchmark::start('system.cf.bootstrap');
        //try to locate bootstrap files for modules 
        foreach (CF::modules() as $module) {
            $bootstrap_path = DOCROOT . 'modules' . DS . $module. DS;
            if (file_exists($bootstrap_path . 'bootstrap' . EXT)) {
                include $bootstrap_path . 'bootstrap' . EXT;
            }
        }
        //try to locate bootstrap files for application 
        $bootstrap_path = DOCROOT . 'application' . DS . CF::app_code() . DS;
        if (file_exists($bootstrap_path . 'bootstrap' . EXT)) {
            include $bootstrap_path . 'bootstrap' . EXT;
        }
        //try to locate bootstrap files for org
        $bootstrap_path .= CF::org_code() . DS;
        if (file_exists($bootstrap_path . 'bootstrap' . EXT)) {
            include $bootstrap_path . 'bootstrap' . EXT;
        }
        CFBenchmark::stop('system.cf.bootstrap');

        // Setup is complete, prevent it from being run again
        $run = TRUE;

        // Stop the environment setup routine
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_setup');
    }

    public static function invoke($uri) {
        $routerData = CFRouter::get_route_data($uri);
        $routes = carr::get($routerData, 'routes');
        $current_uri = carr::get($routerData, 'current_uri');
        $query_string = carr::get($routerData, 'query_string');
        $complete_uri = carr::get($routerData, 'complete_uri');
        $routed_uri = carr::get($routerData, 'routed_uri');
        $url_suffix = carr::get($routerData, 'url_suffix');
        $segments = carr::get($routerData, 'segments');
        $rsegments = carr::get($routerData, 'rsegments');
        $controller = carr::get($routerData, 'controller');
        $controller_dir = carr::get($routerData, 'controller_dir');
        $controller_dir_ucfirst = carr::get($routerData, 'controller_dir_ucfirst');
        $controller_path = carr::get($routerData, 'controller_path');
        $method = carr::get($routerData, 'method');
        $arguments = carr::get($routerData, 'arguments');

        // Include the Controller file
        if (strlen($controller_path) > 0) {
            require_once $controller_path;
        }
        $class_name = '';
        try {
            // Start validation of the controller
            $class_name = str_replace('/', '_', $controller_dir_ucfirst);
            $class_name = 'Controller_' . $class_name . ucfirst($controller);
            $class = new ReflectionClass($class_name);
        } catch (ReflectionException $e) {
            try {
                $class_name = ucfirst($controller) . '_Controller';
                $class = new ReflectionClass($class_name);
                // Start validation of the controller
            } catch (ReflectionException $e) {
                // Controller does not exist

                CFEvent::run('system.404');
            }
        }

        if (isset($class) && ($class->isAbstract() OR ( IN_PRODUCTION AND $class->getConstant('ALLOW_PRODUCTION') == FALSE))) {
            // Controller is not allowed to run in production
            throw new CException('class is abstract or not allowed in production in :class_name', array(':class_name' => $class_name));
        }
        // Create a new controller instance
        if (isset($class)) {
            $controller = $class->newInstance();
        }
        try {
            // Load the controller method
            $method = $class->getMethod($method);

            // Method exists
            if (CFRouter::$method[0] === '_') {
                // Do not allow access to hidden methods
                throw new CException('method :method is hidden methods in :class_name', array(':method' => $method, ':class_name' => $class_name));
            }

            if ($method->isProtected() or $method->isPrivate()) {
                // Do not attempt to invoke protected methods
                throw new ReflectionException('protected controller method');
            }

            // Default arguments
            $arguments = $arguments;
        } catch (ReflectionException $e) {
            // Use __call instead
            $method = $class->getMethod('__call');

            // Use arguments in __call format
            $arguments = array($method, $arguments);
        }

        // Execute the controller method
        return $method->invokeArgs($controller, $arguments);
    }

    /**
     * Loads the controller and initializes it. Runs the pre_controller,
     * post_controller_constructor, and post_controller events. Triggers
     * a system.404 event when the route cannot be mapped to a controller.
     *
     * This method is benchmarked as controller_setup and controller_execution.
     *
     * @return  object  instance of controller
     */
    public static function & instance() {
        $null = NULL;
        if (self::$instance === NULL) {
            CFBenchmark::start(SYSTEM_BENCHMARK . '_controller_setup');
            if (empty(CFRouter::$controller_path)) {
                CF::show_404();
            }
            // Include the Controller file
            if (strlen(CFRouter::$controller_path) > 0) {
                require_once CFRouter::$controller_path;
            }
            try {
                // Start validation of the controller
                $class_name = str_replace('/', '_', CFRouter::$controller_dir_ucfirst);
                $class = new ReflectionClass('Controller_' . $class_name . ucfirst(CFRouter::$controller));
            } catch (ReflectionException $e) {
                try {
                    $class = new ReflectionClass(ucfirst(CFRouter::$controller) . '_Controller');
                    // Start validation of the controller
                } catch (ReflectionException $e) {
                    // Controller does not exist
                    CFEvent::run('system.404');
                    return $null;
                }
            }

            if (isset($class) && ($class->isAbstract() OR ( IN_PRODUCTION AND $class->getConstant('ALLOW_PRODUCTION') == FALSE))) {
                // Controller is not allowed to run in production
                CFEvent::run('system.404');
                return $null;
            }

            // Run system.pre_controller
            CFEvent::run('system.pre_controller');

            // Create a new controller instance
            if (isset($class)) {
                $controller = $class->newInstance();

                if (!isset(self::$instances[CFRouter::$current_uri])) {
                    self::$instances[CFRouter::$current_uri] = $controller;
                }
            }

            // Controller constructor has been executed
            CFEvent::run('system.post_controller_constructor');

            try {
                // Load the controller method
                $method = $class->getMethod(CFRouter::$method);

                // Method exists
                if (CFRouter::$method[0] === '_') {
                    // Do not allow access to hidden methods
                    CFEvent::run('system.404');
                }

                if ($method->isProtected() or $method->isPrivate()) {
                    // Do not attempt to invoke protected methods
                    throw new ReflectionException('protected controller method');
                }

                // Default arguments
                $arguments = CFRouter::$arguments;
            } catch (ReflectionException $e) {
                // Use __call instead
                $method = $class->getMethod('__call');

                // Use arguments in __call format
                $arguments = array(CFRouter::$method, CFRouter::$arguments);
            }

            // Stop the controller setup benchmark
            CFBenchmark::stop(SYSTEM_BENCHMARK . '_controller_setup');

            // Start the controller execution benchmark
            CFBenchmark::start(SYSTEM_BENCHMARK . '_controller_execution');


            // Execute the controller method
            $method->invokeArgs($controller, $arguments);

            // Controller method has been executed
            CFEvent::run('system.post_controller');

            // Stop the controller execution benchmark
            CFBenchmark::stop(SYSTEM_BENCHMARK . '_controller_execution');
        }


        return self::$instance;
    }

    public static function get_dir($directory = '', $domain = null) {
        $include_paths = CF::paths();
        foreach ($include_paths as $p) {
            $path = $p;
            if (strlen($directory) > 0) {
                $path = $p . $directory . DS;
            }
            if (is_dir($path)) {
                return $path;
            }
        }
        return null;
    }

    /**
     * 
     * @deprecated
     * @param string $directory
     * @param string $domain
     * @return array array of directory
     */
    public static function get_dirs($directory, $domain = null) {
        return self::getDirs($directory, $domain);
    }

    /**
     * 
     * @param string $directory
     * @param string $domain
     * @return array array of directory
     */
    public static function getDirs($directory, $domain = null) {
        $include_paths = CF::paths();
        $dirs = array();
        foreach ($include_paths as $p) {
            $path = $p . $directory . DS;
            if (is_dir($path)) {
                $dirs[] = $path;
            }
        }
        return $dirs;
    }

    public static function get_config($filename, $domain = null) {
        $files = self::get_files('config', $filename, $domain);
        $files = array_reverse($files);
        $ret = array();
        foreach ($files as $file) {
            $cfg = include $file;
            $ret = array_merge($ret, $cfg);
        }
        return $ret;
    }

    /**
     * @deprecated Please use getFiles
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @return string[]
     */
    public static function get_files($directory, $filename, $domain = null) {
        return self::getFiles($directory, $filename, $domain);
    }

    /**
     * 
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @return string[]
     */
    public static function getFiles($directory, $filename, $domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        $include_paths = CF::paths($domain);


        $result = array();
        foreach ($include_paths as $path) {
            if (file_exists($path . $directory . DS . $filename . EXT)) {
                $result[] = $path . $directory . DS . $filename . EXT;
            }
        }

        return $result;
    }

    /**
     * 
     * @deprecated
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @return string
     */
    public static function get_file($directory, $filename, $domain = null) {
        return self::getFile($directory, $filename, $domain);
    }

    /**
     * 
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @return string
     */
    public static function getFile($directory, $filename, $domain = null) {
        $files = CF::get_files($directory, $filename, $domain);
        if (count($files) > 0) {
            return $files[0];
        }
        return null;
    }

    /**
     * Get all include paths. APPPATH is the first path, followed by module
     * paths in the order they are configured, follow by the SYSPATH.
     *
     * @param   boolean  re-process the include paths
     * @return  array
     */
    public static function paths($domain = null, $force_reload = false) {
        if ($domain == null) {
            $domain = CF::domain($domain);
        }
        if (!isset(self::$paths[$domain]) || $force_reload) {
            //we try to search all paths for this domain
            $paths = array();
            $theme = CF::theme($domain);
            $org_code = CF::orgCode($domain);
            $app_code = CF::appCode($domain);
            $sharedAppCode = CF::getSharedApp($domain);
            $modules = CF::modules($domain);
            //when this domain is org
            if (strlen($org_code) > 0) {
                //add theme path if theme exists
                if (strlen($theme) > 0) {
                    $paths[] = APPPATH . $app_code . DS . $org_code . DS . "themes" . DS . $theme . DS;
                }
                $paths[] = APPPATH . $app_code . DS . $org_code . DS;
            }
            if (strlen($app_code) > 0) {
                //add theme path if theme exists
                if (strlen($theme) > 0) {
                    $paths[] = APPPATH . $app_code . DS . 'default' . DS . "themes" . DS . $theme . DS;
                }
                $paths[] = APPPATH . $app_code . DS . 'default' . DS;
            }
            foreach ($sharedAppCode as $key => $value) {
                if (strlen($org_code) > 0) {
                    //add theme path if theme exists
                    if (strlen($theme) > 0) {
                        $paths[] = APPPATH . $value . DS . $org_code . DS . "themes" . DS . $theme . DS;
                    }
                    $paths[] = APPPATH . $value . DS . $org_code . DS;
                }
                if (strlen($theme) > 0) {
                    $paths[] = APPPATH . $value . DS . 'default' . DS . "themes" . DS . $theme . DS;
                }
                $paths[] = APPPATH . $value . DS . 'default' . DS;
            }

            foreach ($modules as $module) {
                $paths[] = MODPATH . $module . DS;
            }
            $paths[] = SYSPATH;
            $paths[] = DOCROOT;
            self::$paths[$domain] = $paths;
        }


        return self::$paths[$domain];
    }

    public static function include_paths_theme($process = FALSE) {

        return self::include_paths($process, true);
    }

    /**
     * Get all include paths. APPPATH is the first path, followed by module
     * paths in the order they are configured, follow by the SYSPATH.
     *
     * @param   boolean  re-process the include paths
     * @return  array
     */
    public static function include_paths($process = FALSE, $with_theme = false) {
        return self::paths();
    }

    /**
     * Get a config item or group.
     *
     * @param   string   item name
     * @param   boolean  force a forward slash (/) at the end of the item
     * @param   boolean  is the item required?
     * @return  mixed
     */
    public static function config($group, $default = null, $required = TRUE) {
        $path = null;
        if (strpos($group, '.') !== FALSE) {
            // Split the config group and path
            list($group, $path) = explode('.', $group, 2);
        }

        $config = CConfig::instance($group);

        $value = $config->get($path, $default);


        return $value;
    }

    /**
     * Sets a configuration item, if allowed.
     *
     * @param   string   config key string
     * @param   string   config value
     * @return  boolean
     */
    public static function config_set($key, $value) {
        // Do this to make sure that the config array is already loaded
        self::config($key);

        if (substr($key, 0, 7) === 'routes.') {
            // Routes cannot contain sub keys due to possible dots in regex
            $keys = explode('.', $key, 2);
        } else {
            // Convert dot-noted key string to an array
            $keys = explode('.', $key);
        }

        // Used for recursion
        $conf = & self::$configuration;
        $last = count($keys) - 1;

        foreach ($keys as $i => $k) {
            if ($i === $last) {
                $conf[$k] = $value;
            } else {
                $conf = & $conf[$k];
            }
        }

        if ($key === 'core.modules') {
            // Reprocess the include paths
            self::include_paths(TRUE);
        }

        return TRUE;
    }

    /**
     * Load a config file.
     *
     * @param   string   config filename, without extension
     * @param   boolean  is the file required?
     * @return  array
     */
    public static function config_load($name, $required = TRUE) {
        if ($name === 'core') {
            $found = FALSE;

            // find config file at all available paths
            if ($files = self::find_file('config', 'config', $required)) {
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        require $file;
                        $found = TRUE;
                    }
                }
            }

            if ($found == FALSE) {
                // Load the application configuration file
                if (file_exists(DOCROOT . 'config/config' . EXT)) {
                    require DOCROOT . 'config/config' . EXT;
                    $found = TRUE;
                }
            }

            if (!isset($config['site_domain'])) {
                // Invalid config file
                die('Your C application configuration file is not valid.');
            }

            return $config;
        }

        if (isset(self::$internal_cache['configuration'][$name]))
            return self::$internal_cache['configuration'][$name];

        // Load matching configs
        $configuration = array();

        if ($files = self::find_file('config', $name, $required)) {
            foreach ($files as $file) {
                require $file;

                if (isset($config) AND is_array($config)) {
                    // Merge in configuration
                    $configuration = array_merge($configuration, $config);
                }
            }
        }

        if (!isset(self::$write_cache['configuration'])) {
            // Cache has changed
            self::$write_cache['configuration'] = TRUE;
        }

        return self::$internal_cache['configuration'][$name] = $configuration;
    }

    /**
     * Clears a config group from the cached configuration.
     *
     * @param   string  config group
     * @return  void
     */
    public static function config_clear($group) {
        // Remove the group from config
        unset(self::$configuration[$group], self::$internal_cache['configuration'][$group]);

        if (!isset(self::$write_cache['configuration'])) {
            // Cache has changed
            self::$write_cache['configuration'] = TRUE;
        }
    }

    /**
     * Add a new message to the log.
     *
     * @param   string  type of message
     * @param   string  message text
     * @return  void
     */
    public static function log($level, $message) {
        if (!is_numeric($level)) {
            $level = carr::get(self::$log_levels, $level);
        }
        if (!is_numeric($level)) {
            $level = CLogger::EMERGENCY;
        }
        if ($level <= CF::$log_threshold) {
            CLogger::instance()->add($level, $message);
        }
    }

    /**
     * Save all currently logged messages.
     *
     * @return  void
     */
    public static function log_save() {
        if (empty(self::$log) OR self::$configuration['core']['log_threshold'] < 1)
            return;

        // Filename of the log
        $filename = self::log_directory() . date('Y-m-d') . '.log' . EXT;

        if (!is_file($filename)) {
            // Write the SYSPATH checking header
            file_put_contents($filename, '<?php defined(\'SYSPATH\') or die(\'No direct script access.\'); ?>' . PHP_EOL . PHP_EOL);

            // Prevent external writes
            chmod($filename, 0644);
        }

        // Messages to write
        $messages = array();

        do {
            // Load the next mess
            list ($date, $type, $text) = array_shift(self::$log);

            // Add a new message line
            $messages[] = $date . ' --- ' . $type . ': ' . $text;
        } while (!empty(self::$log));

        // Write messages to log file
        file_put_contents($filename, implode(PHP_EOL, $messages) . PHP_EOL, FILE_APPEND);
    }

    /**
     * Get or set the logging directory.
     *
     * @param   string  new log directory
     * @return  string
     */
    public static function log_directory($dir = NULL) {
        static $directory;

        $dir = CF::get_dir('logs');


        if (!empty($dir)) {
            // Get the directory path
            $dir = realpath($dir);

            if (!is_dir($dir)) {
                mkdir($dir);
            }

            if (is_dir($dir) AND is_writable($dir)) {
                // Change the log directory
                $directory = str_replace('\\', '/', $dir) . '/';
            } else {
                // Log directory is invalid
                throw new CF_Exception('core.log_dir_unwritable', $dir);
            }
        }

        return $directory;
    }

    /**
     * Load data from a simple cache file. This should only be used internally,
     * and is NOT a replacement for the Cache library.
     *
     * @param   string   unique name of cache
     * @param   integer  expiration in seconds
     * @return  mixed
     */
    public static function cache($name, $lifetime) {
        if ($lifetime > 0) {
            $path = self::$internal_cache_path . 'kohana_' . $name;

            if (is_file($path)) {
                // Check the file modification time
                if ((time() - filemtime($path)) < $lifetime) {
                    // Cache is valid! Now, do we need to decrypt it?
                    if (self::$internal_cache_encrypt === TRUE) {
                        $data = file_get_contents($path);

                        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
                        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

                        $decrypted_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::$internal_cache_key, $data, MCRYPT_MODE_ECB, $iv);

                        $cache = unserialize($decrypted_text);

                        // If the key changed, delete the cache file
                        if (!$cache)
                            unlink($path);

                        // If cache is false (as above) return NULL, otherwise, return the cache
                        return ($cache ? $cache : NULL);
                    }
                    else {
                        return unserialize(file_get_contents($path));
                    }
                } else {
                    // Cache is invalid, delete it
                    unlink($path);
                }
            }
        }

        // No cache found
        return NULL;
    }

    /**
     * Save data to a simple cache file. This should only be used internally, and
     * is NOT a replacement for the Cache library.
     *
     * @param   string   cache name
     * @param   mixed    data to cache
     * @param   integer  expiration in seconds
     * @return  boolean
     */
    public static function cache_save($name, $data, $lifetime) {
        if ($lifetime < 1)
            return FALSE;

        $path = self::$internal_cache_path . 'kohana_' . $name;

        if ($data === NULL) {
            // Delete cache
            return (is_file($path) and unlink($path));
        } else {
            // Using encryption? Encrypt the data when we write it
            if (self::$internal_cache_encrypt === TRUE) {
                // Encrypt and write data to cache file
                $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
                $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

                // Serialize and encrypt!
                $encrypted_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::$internal_cache_key, serialize($data), MCRYPT_MODE_ECB, $iv);

                return (bool) file_put_contents($path, $encrypted_text);
            } else {
                // Write data to cache file
                return (bool) file_put_contents($path, serialize($data));
            }
        }
    }

    /**
     * C output handler. Called during ob_clean, ob_flush, and their variants.
     *
     * @param   string  current output buffer
     * @return  string
     */
    public static function output_buffer($output) {
        // Could be flushing, so send headers first
        if (!CFEvent::has_run('system.send_headers')) {
            // Run the send_headers event
            CFEvent::run('system.send_headers');
        }

        self::$output = $output;

        // Set and return the final output
        return self::$output;
    }

    /**
     * Closes all open output buffers, either by flushing or cleaning, and stores the C
     * output buffer for display during shutdown.
     *
     * @param   boolean  disable to clear buffers, rather than flushing
     * @return  void
     */
    public static function close_buffers($flush = TRUE) {
        if (ob_get_level() >= self::$buffer_level) {
            // Set the close function
            $close = ($flush === TRUE) ? 'ob_end_flush' : 'ob_end_clean';

            while (ob_get_level() > self::$buffer_level) {
                // Flush or clean the buffer
                $close();
            }

            // Store the C output buffer
            ob_end_clean();
        }
    }

    /**
     * Triggers the shutdown of C by closing the output buffer, runs the system.display event.
     *
     * @return  void
     */
    public static function shutdown() {
        // Close output buffers
        self::close_buffers(TRUE);

        // Run the output event
        CFEvent::run('system.display', self::$output);

        // Render the final output
        self::render(self::$output);
    }

    /**
     * Inserts global C variables into the generated output and prints it.
     *
     * @param   string  final output that will displayed
     * @return  void
     */
    public static function render($output) {
        if (self::config('core.render_stats') === TRUE) {
            // Fetch memory usage in MB
            $memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;

            // Fetch benchmark for page execution time
            $benchmark = CFBenchmark::get(SYSTEM_BENCHMARK . '_total_execution');

            // Replace the global template variables
            $output = str_replace(
                    array
                (
                '{cf_version}',
                '{cf_codename}',
                '{execution_time}',
                '{memory_usage}',
                '{included_files}',
                    ), array
                (
                CF_VERSION,
                CF_CODENAME,
                $benchmark['time'],
                number_format($memory, 2) . 'MB',
                count(get_included_files()),
                    ), $output
            );
        }

        if ($level = self::config('core.output_compression') AND ini_get('output_handler') !== 'ob_gzhandler' AND (int) ini_get('zlib.output_compression') === 0) {
            if ($level < 1 OR $level > 9) {
                // Normalize the level to be an integer between 1 and 9. This
                // step must be done to prevent gzencode from triggering an error
                $level = max(1, min($level, 9));
            }

            if (stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                $compress = 'gzip';
            } elseif (stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== FALSE) {
                $compress = 'deflate';
            }
        }

        if (isset($compress) AND $level > 0) {
            switch ($compress) {
                case 'gzip':
                    // Compress output using gzip
                    $output = gzencode($output, $level);
                    break;
                case 'deflate':
                    // Compress output using zlib (HTTP deflate)
                    $output = gzdeflate($output, $level);
                    break;
            }

            // This header must be sent with compressed content to prevent
            // browser caches from breaking
            header('Vary: Accept-Encoding');

            // Send the content encoding header
            header('Content-Encoding: ' . $compress);

            // Sending Content-Length in CGI can result in unexpected behavior
            if (stripos(PHP_SAPI, 'cgi') === FALSE) {
                header('Content-Length: ' . strlen($output));
            }
        }

        echo $output;
    }

    /**
     * Displays a 404 page.
     *
     * @throws  C_404_Exception
     * @param   string  URI of page
     * @param   string  custom template
     * @return  void
     */
    public static function show_404($page = FALSE, $template = FALSE) {
        if (CFRouter::$current_uri == 'favicon.ico') {
            return false;
        }
        if (isset($_GET['debug_404'])) {
            try {
                throw new Exception('404');
            } catch (Exception $ex) {
                cdbg::var_dump(nl2br($ex->getTraceAsString()));
            }
        }
        throw new CF_404_Exception($page, $template);
    }

    /**
     * Dual-purpose PHP error and exception handler. Uses the kohana_error_page
     * view to display the message.
     *
     * @param   integer|object  exception object or error code
     * @param   string          error message
     * @param   string          filename
     * @param   integer         line number
     * @return  void
     */
    public static function exception_handler($exception, $message = NULL, $file = NULL, $line = NULL) {

        try {

            // PHP errors have 5 args, always
            $PHP_ERROR = (func_num_args() === 5);
            if (isset($_GET['debug'])) {
                if ($PHP_ERROR) {
                    cdbg::var_dump($message);
                    try {
                        throw new Exception('testing');
                    } catch (Exception $ex) {
                        cdbg::var_dump($ex->getTraceAsString());
                    }
                    die;
                } else {
                    cdbg::var_dump($exception);
                    try {
                        throw new Exception('testing');
                    } catch (Exception $ex) {
                        cdbg::var_dump($ex->getTraceAsString());
                    }
                    die;
                }
            }
            $is404 = false;
            if ($exception instanceof CF_404_Exception) {
                $is404 = true;
            }

            // Test to see if errors should be displayed
            if ($PHP_ERROR AND ( error_reporting() & $exception) === 0)
                return;

            // This is useful for hooks to determine if a page has an error
            self::$has_error = TRUE;

            if (!is_object($exception)) {
                $PHP_ERROR = true;
            }

            // Error handling will use exactly 5 args, every time
            $trace = '';
            $uri = '';

            if ($PHP_ERROR) {

                $code = $exception;
                $type = 'PHP Error';
                $template = 'kohana_error_page';
            } else {
                $code = $exception->getCode();
                $type = get_class($exception);
                $message = $exception->getMessage();
                $file = $exception->getFile();
                $line = $exception->getLine();
                $trace = $exception->getTraceAsString();
                $uri = CFRouter::$current_uri;
                $template = ($exception instanceof CF_Exception) ? $exception->get_template() : 'kohana_error_page';
            }

            if (is_numeric($code)) {
                $codes = self::lang('errors');

                if (!empty($codes[$code])) {
                    list($level, $error, $description) = $codes[$code];
                } else {
                    $level = 1;
                    $error = $PHP_ERROR ? 'Unknown Error' : get_class($exception);
                    $description = '';
                }
            } else {
                // Custom error message, this will never be logged
                $level = 5;
                $error = $code;
                $description = '';
            }

            // Remove the DOCROOT from the path, as a security precaution
            $file = str_replace('\\', '/', realpath($file));
            $file = preg_replace('|^' . preg_quote(DOCROOT) . '|', '', $file);

            if ($level <= self::$log_threshold) {
                // Log the error
                $need_to_log = true;
                if (!$PHP_ERROR) {
                    if ($is404) {
                        $need_to_log = false;
                    }
                }
                if ($need_to_log) {
                    self::log(CLogger::ERROR, self::lang('core.uncaught_exception', $type, $message, $file, $line . " on uri:" . $uri . " with trace:\n" . $trace));
                }
            }

            if ($PHP_ERROR) {
                $description = self::lang('errors.' . E_RECOVERABLE_ERROR);
                $description = is_array($description) ? $description[2] : '';

                if (!headers_sent()) {
                    // Send the 500 header
                    header('HTTP/1.1 500 Internal Server Error');
                }
            } else {
                if (method_exists($exception, 'send_headers') AND ! headers_sent()) {
                    // Send the headers if they have not already been sent
                    $exception->send_headers();
                } else {
                    if (!headers_sent()) {
                        // Send the 500 header
                        header('HTTP/1.1 500 Internal Server Error');
                    }
                }
            }

            // Close all output buffers except for C
            while (ob_get_level() > self::$buffer_level) {
                ob_end_clean();
            }

            // Test if display_errors is on
            if (self::config('app.error_disabled') !== TRUE) {
                /*
                  if (!IN_PRODUCTION AND $line != FALSE) {
                  // Remove the first entry of debug_backtrace(), it is the exception_handler call
                  $trace = $PHP_ERROR ? array_slice(debug_backtrace(), 1) : $exception->getTrace();

                  // Beautify backtrace
                  $trace = self::backtrace($trace);
                  }
                 * 
                 */

                if (IN_PRODUCTION && !$is404) {
                    $data = array(
                        'description' => $description,
                        'error' => $error,
                        'message' => $message,
                        'show_debug_error' => '1',
                    );

                    $view = CView::factory('kohana_error_page', $data);
                    try {
                        cmail::error_mail($view->render());
                    } catch (Exception $ex) {
                        clog::log('error_mail.log', 'error', CF::domain() . " - " . $ex->getMessage());
                    }
                }

                // Load the error
                $custom_error = false;
                if (IN_PRODUCTION) {
                    if (!isset($_GET['show_error'])) {
                        if (CView::exists('ccore/error_page')) {
                            $custom_error = true;
                            echo CView::factory('ccore/error_page')->render();
                        }
                    }
                }
                if (!$custom_error) {
                    require self::find_file('views', empty($template) ? 'kohana_error_page' : $template);
                }
            } else {
                // Get the i18n messages
                $error = self::lang('core.generic_error');
                $message = self::lang('core.errors_disabled', curl::site(), curl::site(CFRouter::$current_uri));

                // Load the errors_disabled view
                require self::find_file('views', 'kohana_error_disabled');
            }

            if (!CFEvent::has_run('system.shutdown')) {
                // Run the shutdown even to ensure a clean exit
                CFEvent::run('system.shutdown');
            }

            // Turn off error reporting
            error_reporting(0);
            exit;
        } catch (Exception $e) {

            if (IN_PRODUCTION) {
                if (isset($_GET['debug'])) {
                    die('Fatal Error: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                } else {
                    die('Fatal Error');
                }
            } else {
                die('Fatal Error: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            }
        }
    }

    /**
     * Provides class auto-loading.
     *
     * @throws  CF_Exception
     * @param   string  name of class
     * @return  bool
     */
    public static function auto_load($class, $directory = 'libraries') {
        if (class_exists($class, FALSE))
            return TRUE;

        if (($suffix = strrpos($class, '_')) > 0) {
            // Find the class suffix
            $suffix = substr($class, $suffix + 1);
        } else {
            // No suffix
            $suffix = FALSE;
        }

        if ($suffix === 'Core') {
            $type = 'libraries';
            $file = substr($class, 0, -5);
        } elseif ($suffix === 'Controller') {
            $type = 'controllers';
            // Lowercase filename
            $file = strtolower(substr($class, 0, -11));
        } elseif ($suffix === 'Model') {
            $type = 'models';
            // Lowercase filename
            $file = strtolower(substr($class, 0, -6));
        } elseif ($suffix === 'Driver') {
            $type = 'libraries/drivers';
            $file = str_replace('_', '/', substr($class, 0, -7));
        } elseif ($suffix === 'Interface') {
            $type = 'interface';
            $file = str_replace('_', '/', substr($class, 0, -10));
//            die($file);
        } else {
            // This could be either a library or a helper, but libraries must
            // always be capitalized, so we check if the first character is
            // uppercase. If it is, we are loading a library, not a helper.
            $type = ($class[0] < 'a') ? 'libraries' : 'helpers';
            $file = $class;
        }

        $class_not_found = FALSE;

        if ($filename = self::find_file($type, $file)) {
            require $filename;
            $class_not_found = TRUE;
            return TRUE;
        }

        if (!$class_not_found) {
            // Transform the class name according to PSR-0
            $routing_class = ltrim($class, '\\');
            $routing_file = '';
            $namespace = '';


            $is_namespace = false;
            if ($last_namespace_position = strripos($routing_class, '\\')) {
                $is_namespace = true;
                $namespace = substr($routing_class, 0, $last_namespace_position);

                $routing_class = substr($routing_class, $last_namespace_position + 1);
                $routing_file = str_replace('\\', DS, $namespace) . DS;
            }



            $routing_file .= str_replace('_', DS, $routing_class);


            if (substr($routing_file, strlen($routing_file) - 1, 1) == DS) {
                $routing_file = substr($routing_file, 0, strlen($routing_file) - 1) . '_';
            }

            if ($directory == 'libraries') {
                // find file at vendor first
                if ($path = self::find_file('vendor', $routing_file)) {
                    // Load the class file
                    require $path;

                    if (class_exists($class)) {
                        $class_not_found = TRUE;
                        return TRUE;
                    }
                }
            }
            // find file at libraries
            if ($path = self::find_file($directory, $routing_file)) {
                // Load the class file
                require $path;
                $class_not_found = TRUE;
                return TRUE;
            }

            // check route file at helpers
            if (!$class_not_found) {
                $temp_routing_file = explode(DS, $routing_file);
                if (strtolower($temp_routing_file[0]) == 'helpers') {
                    $temp_routing_file[0] = 'helpers';
                    $routing_file = str_replace('Helpers' . DS, '', $routing_file);
                    $directory = 'helpers';
                    if ($path = self::find_file($directory, $routing_file)) {
                        // Load the class file

                        require $path;
                        $class_not_found = TRUE;
                        return TRUE;
                    }
                }
            }
        }



        if (!$class_not_found) {
            // The class could not be found
            return FALSE;
        }

        if ($filename = self::find_file($type, self::$configuration['core']['extension_prefix'] . $class)) {
            // Load the class extension
            require $filename;
        } elseif ($suffix !== 'Core' AND class_exists($class . '_Core', FALSE)) {
            // Class extension to be evaluated
            $extension = 'class ' . $class . ' extends ' . $class . '_Core { }';

            // Start class analysis
            $core = new ReflectionClass($class . '_Core');

            if ($core->isAbstract()) {
                // Make the extension abstract
                $extension = 'abstract ' . $extension;
            }

            // Transparent class extensions are handled using eval. This is
            // a disgusting hack, but it gets the job done.
            eval($extension);
        }

        return TRUE;
    }

    public static function domain() {
        $domain = '';
        if (PHP_SAPI === 'cli') {
            // Command line requires a bit of hacking
            if (isset($_SERVER['argv'][2])) {
                $domain = $_SERVER['argv'][2];
            }
        } else {
            $domain = $_SERVER["SERVER_NAME"];
        }
        return $domain;
    }

    /**
     * Find a resource file in a given directory. Files will be located according
     * to the order of the include paths. Configuration and i18n files will be
     * returned in reverse order.
     *
     * @throws  CF_Exception  if file is required and not found
     * @param   string   directory to search in
     * @param   string   filename to look for (without extension)
     * @param   boolean  file required
     * @param   string   file extension
     * @return  array    if the type is config, i18n or l10n
     * @return  string   if the file is found
     * @return  FALSE    if the file is not found
     */
    public static function find_file($directory, $filename, $required = FALSE, $ext = FALSE) {
        // NOTE: This test MUST be not be a strict comparison (===), or empty
        // extensions will be allowed!
        if ($ext == '') {
            // Use the default extension
            $ext = EXT;
        } else {
            // Add a period before the extension
            $ext = '.' . $ext;
        }

        // Search path
        $search = $directory . '/' . $filename . $ext;

        if (isset(self::$internal_cache['find_file_paths'][$search]))
            return self::$internal_cache['find_file_paths'][$search];

        // Load include paths
        $paths = self::paths();

        // Nothing found, yet
        $found = NULL;

        if ($directory === 'config' OR $directory === 'i18n') {
            // Search in reverse, for merging
            $paths = array_reverse($paths);

            foreach ($paths as $path) {
                if (is_file($path . $search)) {
                    // A matching file has been found
                    $found[] = $path . $search;
                }
            }
        } else {

            foreach ($paths as $path) {
                if (is_file($path . $search)) {
                    // A matching file has been found
                    $found = $path . $search;

                    // Stop searching
                    break;
                }
            }
        }

        if ($found === NULL) {
            if ($required === TRUE) {
                // Directory i18n key
                $directory = 'core.' . inflector::singular($directory);

                // If the file is required, throw an exception
                throw new CF_Exception('core.resource_not_found', self::lang($directory), $filename);
            } else {
                // Nothing was found, return FALSE
                $found = FALSE;
            }
        }

        if (!isset(self::$write_cache['find_file_paths'])) {
            // Write cache at shutdown
            self::$write_cache['find_file_paths'] = TRUE;
        }

        return self::$internal_cache['find_file_paths'][$search] = $found;
    }

    /**
     * Lists all files and directories in a resource path.
     *
     * @param   string   directory to search
     * @param   boolean  list all files to the maximum depth?
     * @param   string   full path to search (used for recursion, *never* set this manually)
     * @return  array    filenames and directories
     */
    public static function list_files($directory, $recursive = FALSE, $path = FALSE) {
        $files = array();

        if ($path === FALSE) {
            $paths = array_reverse(self::include_paths());

            foreach ($paths as $path) {
                // Recursively get and merge all files
                $files = array_merge($files, self::list_files($directory, $recursive, $path . $directory));
            }
        } else {
            $path = rtrim($path, '/') . '/';

            if (is_readable($path)) {
                $items = (array) glob($path . '*');

                if (!empty($items)) {
                    foreach ($items as $index => $item) {
                        $files[] = $item = str_replace('\\', '/', $item);

                        // Handle recursion
                        if (is_dir($item) AND $recursive == TRUE) {
                            // Filename should only be the basename
                            $item = pathinfo($item, PATHINFO_BASENAME);

                            // Append sub-directory search
                            $files = array_merge($files, self::list_files($directory, TRUE, $path . $item));
                        }
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Fetch an i18n language item.
     *
     * @param   string  language key to fetch
     * @param   array   additional information to insert into the line
     * @return  string  i18n language string, or the requested key if the i18n item is not found
     */
    public static function lang($key, $args = array()) {
        // Extract the main group from the key
        $group = explode('.', $key, 2);
        $group = $group[0];

        // Get locale name
        $locale = self::config('locale.language.0');

        if (!isset(self::$internal_cache['language'][$locale][$group])) {
            // Messages for this group
            $messages = array();

            if ($files = self::find_file('i18n', $locale . '/' . $group)) {
                foreach ($files as $file) {
                    include $file;

                    // Merge in configuration
                    if (!empty($lang) AND is_array($lang)) {
                        foreach ($lang as $k => $v) {
                            $messages[$k] = $v;
                        }
                    }
                }
            }

            if (!isset(self::$write_cache['language'])) {
                // Write language cache
                self::$write_cache['language'] = TRUE;
            }

            self::$internal_cache['language'][$locale][$group] = $messages;
        }

        // Get the line from cache
        $line = self::key_string(self::$internal_cache['language'][$locale], $key);

        if ($line === NULL) {
            self::log('error', 'Missing i18n entry ' . $key . ' for language ' . $locale);

            // Return the key string as fallback
            return $key;
        }

        if (is_string($line) AND func_num_args() > 1) {
            $args = array_slice(func_get_args(), 1);

            // Add the arguments into the line
            $line = vsprintf($line, is_array($args[0]) ? $args[0] : $args);
        }

        return $line;
    }

    /**
     * Returns the value of a key, defined by a 'dot-noted' string, from an array.
     *
     * @param   array   array to search
     * @param   string  dot-noted string: foo.bar.baz
     * @return  string  if the key is found
     * @return  void    if the key is not found
     */
    public static function key_string($array, $keys) {
        if (empty($array))
            return NULL;

        // Prepare for loop
        $keys = explode('.', $keys);

        do {
            // Get the next key
            $key = array_shift($keys);

            if (isset($array[$key])) {
                if (is_array($array[$key]) AND ! empty($keys)) {
                    // Dig down to prepare the next loop
                    $array = $array[$key];
                } else {
                    // Requested key was found
                    return $array[$key];
                }
            } else {
                // Requested key is not set
                break;
            }
        } while (!empty($keys));

        return NULL;
    }

    /**
     * Sets values in an array by using a 'dot-noted' string.
     *
     * @param   array   array to set keys in (reference)
     * @param   string  dot-noted string: foo.bar.baz
     * @return  mixed   fill value for the key
     * @return  void
     */
    public static function key_string_set(& $array, $keys, $fill = NULL) {
        if (is_object($array) AND ( $array instanceof ArrayObject)) {
            // Copy the array
            $array_copy = $array->getArrayCopy();

            // Is an object
            $array_object = TRUE;
        } else {
            if (!is_array($array)) {
                // Must always be an array
                $array = (array) $array;
            }

            // Copy is a reference to the array
            $array_copy = & $array;
        }

        if (empty($keys))
            return $array;

        // Create keys
        $keys = explode('.', $keys);

        // Create reference to the array
        $row = & $array_copy;

        for ($i = 0, $end = count($keys) - 1; $i <= $end; $i++) {
            // Get the current key
            $key = $keys[$i];

            if (!isset($row[$key])) {
                if (isset($keys[$i + 1])) {
                    // Make the value an array
                    $row[$key] = array();
                } else {
                    // Add the fill key
                    $row[$key] = $fill;
                }
            } elseif (isset($keys[$i + 1])) {
                // Make the value an array
                $row[$key] = (array) $row[$key];
            }

            // Go down a level, creating a new row reference
            $row = & $row[$key];
        }

        if (isset($array_object)) {
            // Swap the array back in
            $array->exchangeArray($array_copy);
        }
    }

    /**
     * Retrieves current user agent information:
     * keys:  browser, version, platform, mobile, robot, referrer, languages, charsets
     * tests: is_browser, is_mobile, is_robot, accept_lang, accept_charset
     *
     * @param   string   key or test name
     * @param   string   used with "accept" tests: user_agent(accept_lang, en)
     * @return  array    languages and charsets
     * @return  string   all other keys
     * @return  boolean  all tests
     */
    public static function user_agent($key = 'agent', $compare = NULL) {
        static $info;

        // Return the raw string
        if ($key === 'agent')
            return self::$user_agent;

        if ($info === NULL) {
            // Parse the user agent and extract basic information
            $agents = self::config('user_agents');

            foreach ($agents as $type => $data) {
                foreach ($data as $agent => $name) {
                    if (stripos(self::$user_agent, $agent) !== FALSE) {
                        if ($type === 'browser' AND preg_match('|' . preg_quote($agent) . '[^0-9.]*+([0-9.][0-9.a-z]*)|i', self::$user_agent, $match)) {
                            // Set the browser version
                            $info['version'] = $match[1];
                        }

                        // Set the agent name
                        $info[$type] = $name;
                        break;
                    }
                }
            }
        }

        if (empty($info[$key])) {
            switch ($key) {
                case 'is_robot':
                case 'is_browser':
                case 'is_mobile':
                    // A boolean result
                    $return = !empty($info[substr($key, 3)]);
                    break;
                case 'languages':
                    $return = array();
                    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                        if (preg_match_all('/[-a-z]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])), $matches)) {
                            // Found a result
                            $return = $matches[0];
                        }
                    }
                    break;
                case 'charsets':
                    $return = array();
                    if (!empty($_SERVER['HTTP_ACCEPT_CHARSET'])) {
                        if (preg_match_all('/[-a-z0-9]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET'])), $matches)) {
                            // Found a result
                            $return = $matches[0];
                        }
                    }
                    break;
                case 'referrer':
                    if (!empty($_SERVER['HTTP_REFERER'])) {
                        // Found a result
                        $return = trim($_SERVER['HTTP_REFERER']);
                    }
                    break;
            }

            // Cache the return value
            isset($return) and $info[$key] = $return;
        }

        if (!empty($compare)) {
            // The comparison must always be lowercase
            $compare = strtolower($compare);

            switch ($key) {
                case 'accept_lang':
                    // Check if the lange is accepted
                    return in_array($compare, self::user_agent('languages'));
                    break;
                case 'accept_charset':
                    // Check if the charset is accepted
                    return in_array($compare, self::user_agent('charsets'));
                    break;
                default:
                    // Invalid comparison
                    return FALSE;
                    break;
            }
        }

        // Return the key, if set
        return isset($info[$key]) ? $info[$key] : NULL;
    }

    /**
     * Quick debugging of any variable. Any number of parameters can be set.
     *
     * @return  string
     */
    public static function debug() {
        if (func_num_args() === 0)
            return;

        // Get params
        $params = func_get_args();
        $output = array();

        foreach ($params as $var) {
            $output[] = '<pre>(' . gettype($var) . ') ' . chtml::specialchars(print_r($var, TRUE)) . '</pre>';
        }

        return implode("\n", $output);
    }

    /**
     * Checks if given data is file, handles mixed input
     *
     * @param  mixed $value
     * @return boolean
     */
    private static function is_file($value) {
        $value = strval(str_replace("\0", "", $value));

        return is_file($value);
    }

    /**
     * Displays nice backtrace information.
     * @see http://php.net/debug_backtrace
     *
     * @param   array   backtrace generated by an exception or debug_backtrace
     * @return  string
     */
    public static function backtrace($trace) {
        if (!is_array($trace))
            return;

        // Final output
        $output = array();

        foreach ($trace as $entry) {
            $temp = '<li>';

            if (isset($entry['file'])) {
                $temp .= self::lang('core.error_file_line', preg_replace('!^' . preg_quote(DOCROOT) . '!', '', $entry['file']), $entry['line']);
            }

            $temp .= '<pre>';

            if (isset($entry['class'])) {
                // Add class and call type
                $temp .= $entry['class'] . $entry['type'];
            }

            // Add function
            $temp .= $entry['function'] . '( ';

            // Add function args
            if (isset($entry['args']) AND is_array($entry['args'])) {
                // Separator starts as nothing
                $sep = '';

                while ($arg = array_shift($entry['args'])) {
                    if (is_string($arg) AND self::is_file($arg)) {
                        // Remove docroot from filename
                        $arg = preg_replace('!^' . preg_quote(DOCROOT) . '!', '', $arg);
                    }

                    $temp .= $sep . chtml::specialchars(print_r($arg, TRUE));

                    // Change separator to a comma
                    $sep = ', ';
                }
            }

            $temp .= ' )</pre></li>';

            $output[] = $temp;
        }

        return '<ul class="backtrace">' . implode("\n", $output) . '</ul>';
    }

    /**
     * Saves the internal caches: configuration, include paths, etc.
     *
     * @return  boolean
     */
    public static function internal_cache_save() {
        if (!is_array(self::$write_cache))
            return FALSE;

        // Get internal cache names
        $caches = array_keys(self::$write_cache);

        // Nothing written
        $written = FALSE;

        foreach ($caches as $cache) {
            if (isset(self::$internal_cache[$cache])) {
                // Write the cache file
                self::cache_save($cache, self::$internal_cache[$cache], self::$configuration['core']['internal_cache']);

                // A cache has been written
                $written = TRUE;
            }
        }

        return $written;
    }

    /**
     * Get data domain
     */
    public static function data($domain = null) {
        $domain = $domain == null ? self::domain() : $domain;
        if (!isset(self::$data[$domain])) {
            self::$data[$domain] = CFData::domain($domain);
            if (self::$data[$domain] == null) {
                //try to locate wildcard subdomain
                $wildcard_domain = implode('.', array('_') + array_slice(explode('.', $domain), 0));
                self::$data[$domain] = CFData::domain($wildcard_domain);
            }
        }
        return self::$data[$domain];
    }

    /**
     * Get application id for domain
     * This function is deprecated, use CF::appId
     *
     * @return  string
     */
    public static function app_id($domain = null) {
        return self::appId($domain);
    }

    /**
     * Get application id for domain
     *
     * @return  string
     */
    public static function appId($domain = null) {
        $data = self::data($domain);
        return isset($data['app_id']) ? $data['app_id'] : null;
    }

    /**
     * Get application code for domain
     * This function is deprecated, use CF::appCode
     * 
     * @deprecated
     * @return  string
     */
    public static function app_code($domain = null) {
        return self::appCode($domain);
    }

    /**
     * Get application code for domain
     *
     * @return  string
     */
    public static function appCode($domain = null) {
        $data = self::data($domain);
        return isset($data['app_code']) ? $data['app_code'] : null;
    }

    /**
     * Get org id for domain
     * This function is deprecated, use CF::orgId
     * 
     * @deprecated
     * @param string $domain
     * @return int
     */
    public static function org_id($domain = null) {
        return self::orgId($domain);
    }

    /**
     * Get org id for domain
     * 
     * @param string $domain
     * @return int
     */
    public static function orgId($domain = null) {
        $data = self::data($domain);
        return isset($data['org_id']) ? $data['org_id'] : null;
    }

    /**
     * Get org code for this domain
     * This function is deprecated, use CF::orgCode
     *
     * @deprecated
     * @param string $domain
     * @return string
     */
    public static function org_code($domain = null) {
        return self::orgCode($domain);
    }

    /**
     * Get org code for this domain
     * 
     * @param string $domain
     * @return string
     */
    public static function orgCode($domain = null) {
        $data = self::data($domain);
        return isset($data['org_code']) ? $data['org_code'] : null;
    }

    /**
     * Add Shared App in runtime
     * This function is deprecated, use CF::addSharedApp
     * 
     * @deprecated
     * @param string $app_code
     */
    public static function add_shared_app_code($app_code) {
        return self::addSharedApp($app_code);
    }

    /**
     * Add Shared App in runtime
     * 
     * @param string $app_code
     */
    public static function addSharedApp($appCode) {
        if (!in_array($appCode, self::$sharedAppCode)) {
            self::$sharedAppCode[] = $appCode;
            //do force reload
            self::paths(null, true);
        }
    }

    /**
     * Get shared application code for this domain
     *
     * @param string $domain
     * @return  array
     */
    public static function getSharedApp($domain = null) {
        $data = self::data($domain);
        if (!isset($data['shared_app_code'])) {
            $data['shared_app_code'] = array();
        }

        $data['shared_app_code'] = array_merge($data['shared_app_code'], self::$sharedAppCode);


        return isset($data['shared_app_code']) ? $data['shared_app_code'] : array();
    }

    /**
     * Get theme for this domain
     *
     * @return  array
     */
    public static function theme($domain = null) {
        $data = self::data($domain);
        return isset($data['theme']) ? $data['theme'] : null;
    }

    /**
     * Get modules for this domain
     *
     * @return  array
     */
    public static function modules($domain = null) {
        $data = self::data($domain);
        return isset($data['modules']) ? $data['modules'] : array('cresenity');
    }

    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @return mixed
     */
    public static function tap($value, $callback = null) {
        if (is_null($callback)) {
            return new HigherOrderTapProxy($value);
        }

        $callback($value);

        return $value;
    }

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    public static function class_basename($class) {
        $class = is_object($class) ? get_class($class) : $class;

        $basename = basename(str_replace('\\', '/', $class));
        $basename = carr::last(explode("_", $basename));
        return $basename;
    }

    /**
     * Returns all traits used by a class, its subclasses and trait of their traits.
     *
     * @param  object|string  $class
     * @return array
     */
    public static function class_uses_recursive($class) {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_merge([$class => $class], class_parents($class)) as $class) {
            $results += self::trait_uses_recursive($class);
        }

        return array_unique($results);
    }

    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  string  $trait
     * @return array
     */
    public static function trait_uses_recursive($trait) {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += self::trait_uses_recursive($trait);
        }

        return $traits;
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function value($value) {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed   $target
     * @param  string|array  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($target, $key, $default = null) {
        if (is_null($key)) {
            return $target;
        }
        $key = is_array($key) ? $key : explode('.', $key);
        while (!is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if ($target instanceof CCollection) {
                    $target = $target->all();
                } elseif (!is_array($target)) {
                    return CF::value($default);
                }
                $result = carr::pluck($target, $key);
                return in_array('*', $key) ? carr::collapse($result) : $result;
            }
            if (carr::accessible($target) && carr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return CF::value($default);
            }
        }
        return $target;
    }

    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function set(&$target, $key, $value, $overwrite = true) {
        $segments = is_array($key) ? $key : explode('.', $key);
        if (($segment = array_shift($segments)) === '*') {
            if (!carr::accessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    CF::set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (carr::accessible($target)) {
            if ($segments) {
                if (!carr::exists($target, $segment)) {
                    $target[$segment] = [];
                }
                CF::set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                CF::set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                CF::set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }
        return $target;
    }

    /**
     * Create a collection from the given value.
     *
     * @param  mixed  $value
     * @return CCollection
     */
    public static function collect($value = null) {
        return new CCollection($value);
    }

    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @return mixed
     */
    public static function with($value, callable $callback = null) {
        return is_null($callback) ? $value : $callback($value);
    }

}

// End C

/**
 * Creates a generic i18n exception.
 */
class CF_Exception extends Exception {

    // Template file
    protected $template = 'kohana_error_page';
    // Header
    protected $header = FALSE;
    // Error code
    protected $code = E_CF;

    /**
     * Set exception message.
     *
     * @param  string  i18n language key for the message
     * @param  array   addition line parameters
     */
    public function __construct($error) {

        $args = array_slice(func_get_args(), 1);

        // Fetch the error message
        $message = CF::lang($error, $args);

        if ($message === $error OR empty($message)) {
            // Unable to locate the message for the error
            $message = 'Unknown Exception: ' . $error;
        }

        // Sets $this->message the proper way
        parent::__construct($message);
    }

    /**
     * Magic method for converting an object to a string.
     *
     * @return  string  i18n message
     */
    public function __toString() {
        return (string) $this->message;
    }

    /**
     * Fetch the template name.
     *
     * @return  string
     */
    public function get_template() {

        return $this->template;
    }

    /**
     * Sends an Internal Server Error header.
     *
     * @return  void
     */
    public function send_headers() {
        // Send the 500 header
        header('HTTP/1.1 500 Internal Server Error');
    }

}

// End C Exception

/**
 * Creates a custom exception.
 */
class CF_User_Exception extends CF_Exception {

    /**
     * Set exception title and message.
     *
     * @param   string  exception title string
     * @param   string  exception message string
     * @param   string  custom error template
     */
    public function __construct($title, $message, $template = FALSE) {
        Exception::__construct($message);

        $this->code = $title;

        if ($template !== FALSE) {
            $this->template = $template;
        }
    }

}

// End C PHP Exception

/**
 * Creates a Page Not Found exception.
 */
class CF_404_Exception extends CF_Exception {

    protected $code = E_PAGE_NOT_FOUND;

    /**
     * Set internal properties.
     *
     * @param  string  URL of page
     * @param  string  custom error template
     */
    public function __construct($page = FALSE, $template = FALSE) {
        if ($page === FALSE) {
            // Construct the page URI using Router properties
            $page = CFRouter::$current_uri . CFRouter::$url_suffix . CFRouter::$query_string;
        }

        if ($template == false) {
            if (CView::exists('ccore/404')) {
                $template = 'ccore/404';
            }
        }

        Exception::__construct(CF::lang('core.page_not_found', $page));

        $this->template = $template;
    }

    /**
     * Sends "File Not Found" headers, to emulate server behavior.
     *
     * @return void
     */
    public function send_headers() {
        // Send the 404 header
        header('HTTP/1.1 404 File Not Found');
    }

}

// End C 404 Exception
