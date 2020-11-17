<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CF {

    use CFDeprecatedTrait;

    const CFCLI_CURRENT_DOMAIN_FILE = DOCROOT . 'data' . DS . 'current-domain';
    // Security check that is added to all generated PHP files
    const FILE_SECURITY = '<?php defined(\'SYSPATH\') OR die(\'No direct script access.\');';

    // The singleton instance of the controller (last of the controller)
    public static $instance;
    // The multiple instance of the controller when callback when routing is failed or redirected
    public static $instances;
    // Will be set to TRUE when an exception is caught
    public static $has_error = FALSE;
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
    private static $data;
    private static $sharedAppCode = array();
    private static $translator;

    /**
     * @var  CLogger  logging object
     */
    public static $logger;

    /**
     * @var  CConfig  config object
     */
    public static $config;

    /**
     * 
     * @return bool
     */
    public static function isProduction() {
        return defined('IN_PRODUCTION') && IN_PRODUCTION;
    }

    /**
     * 
     * @return bool
     */
    public static function isCli() {
        return defined('CFCLI');
    }

    /**
     * Check given domain exists or not
     * 
     * @param string $domain
     * @return bool
     */
    public static function domainExists($domain) {
        return CFData::domain($domain) !== null;
    }

    /**
     * 
     * @param type $domain
     * @param array $domainData
     * @return bool
     */
    public static function createDomain($domain, array $domainData) {
        if (!static::domainExists($domain)) {
            CFData::set($domain, $domainData, 'domain');
            return true;
        }
        return false;
    }

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
        if ($run === TRUE) {
            return;
        }

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



        // Set autoloader
        spl_autoload_register(array('CF', 'autoLoad'));

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

        if (isset(self::$configuration['core']) && isset(self::$configuration['core']['log_threshold']) && self::$configuration['core']['log_threshold'] > 0) {
            // Set the log directory
            self::log_directory(self::$configuration['core']['log_directory']);

            // Enable log writing at shutdown
            register_shutdown_function(array(__CLASS__, 'log_save'));
        }


        // Enable CF 404 pages
        CFEvent::add('system.404', array('CF', 'show404'));

        static::loadBootstrapFiles();

        // Setup is complete, prevent it from being run again
        $run = TRUE;

        // Stop the environment setup routine

        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_setup');
    }

    /**
     * load all bootstrap files
     */
    private static function loadBootstrapFiles() {
        CFBenchmark::start(SYSTEM_BENCHMARK . '_environment_bootstrap');


        //try to locate bootstrap files for modules 
        foreach (CF::modules() as $module) {
            $bootstrapPath = DOCROOT . 'modules' . DS . $module . DS;
            if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
                include $bootstrapPath . 'bootstrap' . EXT;
            }
        }


        //try to locate bootstrap files for application 
        $bootstrapPath = DOCROOT . 'application' . DS . CF::appCode() . DS;
        if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
            include $bootstrapPath . 'bootstrap' . EXT;
        }


        //try to locate bootstrap files for org
        $bootstrapPath .= CF::orgCode() . DS;
        if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
            include $bootstrapPath . 'bootstrap' . EXT;
        }
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_bootstrap');
    }

    public static function invoke($uri) {

        $routerData = CFRouter::getRouteData($uri);
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
    public static function getDir($directory = '', $domain = null) {
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
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @return string
     */
    public static function getFile($directory, $filename, $domain = null) {
        $files = CF::getFiles($directory, $filename, $domain);
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
                $paths[] = APPPATH . $app_code . DS . $org_code . DS;
            }
            if (strlen($app_code) > 0) {
                //add theme path if theme exists
                $paths[] = APPPATH . $app_code . DS . 'default' . DS;
            }
            foreach ($sharedAppCode as $key => $value) {
                if (strlen($org_code) > 0) {
                    //add theme path if theme exists
                    $paths[] = APPPATH . $value . DS . $org_code . DS;
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
            if ($files = self::findFile('config', 'config', $required)) {
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

        if ($files = self::findFile('config', $name, $required)) {
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
            if (class_exists('CLogger')) {
                CLogger::instance()->add($level, $message);
            }
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

        $dir = CF::getDir('logs');


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
     * Inserts global C variables into the generated output and prints it.
     *
     * @param   string  final output that will displayed
     * @return  void
     */
    /*
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
     * 
     */

    /**
     * Displays a 404 page.
     *
     * @throws  C_404_Exception
     * @param   string  URI of page
     * @param   string  custom template
     * @return  void
     */
    public static function show404($page = FALSE, $template = FALSE) {

        return CF::abort(404);
    }

    public static function abort($code, $message = '', array $headers = []) {
        if ($code instanceof CHTTP_Response) {
            throw new CHttp_Exception_ResponseException($code);
        } elseif ($code instanceof CInterface_Responsable) {
            throw new CHttp_Exception_ResponseException($code->toResponse(CHTTP::request()));
        }

        if ($code == 404) {
            throw new CHTTP_Exception_NotFoundHttpException($message);
        }

        throw new CHTTP_Exception_HttpException($code, $message, null, $headers);
    }

    /**
     * Return a new response from the application.
     *
     * @param  CView|string|array|null  $content
     * @param  int  $status
     * @param  array  $headers
     * @return CHTTP_Response|CHTTP_ResponseFactory
     */
    public static function response($content = '', $status = 200, array $headers = []) {
        $factory = CHTTP::responseFactory();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }

    /**
     * Provides class auto-loading.
     *
     * @throws  CF_Exception
     * @param   string  name of class
     * @return  bool
     */
    public static function autoLoad($class, $directory = 'libraries') {
        if (class_exists($class, FALSE)) {
            return TRUE;
        }

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

        if ($filename = self::findFile($type, $file)) {
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
                if ($path = self::findFile('vendor', $routing_file)) {
                    // Load the class file
                    require $path;

                    if (class_exists($class)) {
                        $class_not_found = TRUE;
                        return TRUE;
                    }
                }
            }
            // find file at libraries
            if ($path = self::findFile($directory, $routing_file)) {
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
                    if ($path = self::findFile($directory, $routing_file)) {
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

        if ($filename = self::findFile($type, self::$configuration['core']['extension_prefix'] . $class)) {
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

    public static function cliDomain() {
        $domain = null;
        if (file_exists(static::CFCLI_CURRENT_DOMAIN_FILE)) {
            $domain = file_get_contents(static::CFCLI_CURRENT_DOMAIN_FILE);
        }
        return $domain;
    }

    public static function domain() {
        $domain = '';
        if (PHP_SAPI === 'cli') {
            // Command line requires a bit of hacking
            if (defined('CFCLI')) {
                $domain = static::cliDomain();
            } else {
                if (isset($_SERVER['argv'][2])) {
                    $domain = $_SERVER['argv'][2];
                }
            }
        } else {
            if (isset($_SERVER["SERVER_NAME"])) {
                $domain = $_SERVER["SERVER_NAME"];
            }
            if (strlen($domain) == 0) {
                if (isset($_SERVER['HTTP_HOST'])) {
                    $domain = $_SERVER["HTTP_HOST"];
                }
            }
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
    public static function findFile($directory, $filename, $required = FALSE, $ext = FALSE) {
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

            if ($files = self::findFile('i18n', $locale . '/' . $group)) {
                foreach ($files as $file) {
                    $lang = include $file;

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

                    $temp .= $sep . chtml::specialchars(@print_r($arg, TRUE));

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
     * Get data domain
     */
    public static function data($domain = null) {
        $domain = $domain == null ? self::domain() : $domain;
        if (!isset(self::$data[$domain])) {
            self::$data[$domain] = CFData::domain($domain);
            if (self::$data[$domain] == null) {
                //try to locate wildcard subdomain
                $wildcardDomain = implode('.', array('$') + array_slice(explode('.', $domain), 0));

                self::$data[$domain] = CFData::domain($wildcardDomain);
            }
        }
        return self::$data[$domain];
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
     *
     * @return  string
     */
    public static function appCode($domain = null) {
        $data = self::data($domain);
        return isset($data['app_code']) ? $data['app_code'] : null;
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
        return c::tap($value, $callback);
    }

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    public static function classBasename($class) {
        return c::classBasename($class);
    }

    /**
     * Returns all traits used by a class, its subclasses and trait of their traits.
     *
     * @param  object|string  $class
     * @return array
     */
    public static function classUsesRecursive($class) {
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
    public static function traitUsesRecursive($trait) {
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
        return c::collect($value);
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

    /**
     * Determine if a value is "filled".
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function filled($value) {
        return !static::blank($value);
    }

    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function blank($value) {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }

    public static function version() {
        return CF_VERSION;
    }

    public static function codeName() {
        return CF_CODENAME;
    }

    public static function appPath($domain = null) {

        $appCode = static::appCode($domain);

        return DOCROOT . 'application/' . $appCode . '/';
    }

    public static function currentController() {
        return static::$instance;
    }

    /** list of deprecated function */
}

// End CF

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
