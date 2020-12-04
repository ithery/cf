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
    // The current user agent
    public static $user_agent;
    // The current locale
    public static $locale;
    // Configuration
    private static $configuration;

    /**
     * Include paths cache
     * @var array
     */
    private static $paths;

    /**
     * Chartset used for this application
     * 
     * @var string
     */
    public static $charset = 'utf-8';

    /* log threshold default , CLogger::LOG_WARNING (4) */
    public static $log_threshold = LOG_WARNING; // 4
    public static $global_xss_filtering = TRUE;
    // Internal caches and write status
    private static $internal_cache = array();

    /**
     * CF Data domain
     * 
     * @var array
     */
    private static $data;

    /**
     * sharedAppCode used for CF
     * 
     * @var array
     */
    private static $sharedAppCode = [];
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
            $timezone = self::config('app.timezone');

            // Set default timezone, due to increased validation of date settings
            // which cause massive amounts of E_NOTICEs to be generated in PHP 5.2+
            date_default_timezone_set(empty($timezone) ? date_default_timezone_get() : $timezone);
        }

        // Restore error reporting
        error_reporting($ER);

        // Send default text/html UTF-8 header
        header('Content-Type: text/html; charset=UTF-8');

        // Load locales
        $locale = self::config('app.locale');


        // Set locale information
        self::$locale = setlocale(LC_ALL, $locale);


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


        $bootstrapPath = DOCROOT . 'system' . DS;
        if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
            include $bootstrapPath . 'bootstrap' . EXT;
        }
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
        if (strlen(CF::orgCode()) > 0) {
            $bootstrapPath .= CF::orgCode() . DS;
            if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
                include $bootstrapPath . 'bootstrap' . EXT;
            }
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

                CF::show404();
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
     * Displays a 404 page.
     *
     * @throws  C_404_Exception
     * @param   string  URI of page
     * @param   string  custom template
     * @return  void
     */
    public static function show404($page = FALSE, $template = FALSE) {
        return c::abort(404);
    }

    /**
     * 
     * @param type $directory
     * @param type $domain
     * @return string
     */
    public static function getDir($directory = '', $domain = null) {
        $includePaths = CF::paths($domain);
        foreach ($includePaths as $p) {
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
        $includePaths = CF::paths($domain);
        $dirs = array();
        foreach ($includePaths as $p) {
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
     * Add a new message to the log.
     *
     * @param   string  type of message
     * @param   string  message text
     * @return  void
     */
    public static function log($level, $message) {
        if (class_exists('CLogger')) {
            CLogger::instance()->add($level, $message);
        }
    }

    /**
     * Provides class auto-loading.
     *
     * @throws  CException
     * @param   string  name of class
     * @return  bool
     */
    public static function autoLoad($class, $directory = 'libraries') {
        if (class_exists($class, FALSE)) {
            return TRUE;
        }
        if (($prefix = strrpos($class, '_')) > 0) {
            // Find the class suffix
            $prefix = substr($class, 0, $prefix);
        } else {
            // No suffix
            $prefix = FALSE;
        }
        if (($suffix = strrpos($class, '_')) > 0) {
            // Find the class suffix
            $suffix = substr($class, $suffix + 1);
        } else {
            // No suffix
            $suffix = FALSE;
        }

        if ($suffix === 'Controller' || $prefix==='Controller') {
            $type = 'controllers';
            // Lowercase filename
            $file = strtolower(substr($class, 0, -11));
            if($prefix) {
                $file = strtolower(substr($class, 11));
            }
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
            $appPath = DOCROOT . 'application' . DS . static::appCode() . DS;
            if (file_exists($appPath . 'composer.json')) {
                $autoLoadPath = $appPath . 'vendor' . DS . 'autoload.php';
                if (file_exists($autoLoadPath)) {
                    $composerLoader = require $autoLoadPath;
                    $result = $composerLoader->loadClass($class);
                    if ($result === true) {
                        return true;
                    }
                }
            }

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

    /**
     * detect CF is running on console in cf command or not
     * 
     * @return boolean
     */
    public static function isCFCli() {
        return defined('CFCLI');
    }

    /**
     * detect CF is running on console or not 
     * 
     * @return type
     */
    public static function isCli() {
        return PHP_SAPI === 'cli';
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
        if (static::isCli()) {
            // Command line requires a bit of hacking
            if (static::isCFCli()) {
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
     * @throws  CException  if file is required and not found
     * @param   string   directory to search in
     * @param   string   filename to look for (without extension)
     * @param   boolean  file required
     * @param   string   file extension
     * @return  array    if the type is config, i18n or l10n
     * @return  string   if the file is found
     * @return  FALSE    if the file is not found
     */
    public static function findFile($directory, $filename, $required = false, $ext = false, $reload = false) {
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

        if (isset(self::$internal_cache['find_file_paths'][$search]) && !$reload) {
            return self::$internal_cache['find_file_paths'][$search];
        }

        // Load include paths
        $paths = self::paths();

        // Nothing found, yet
        $found = NULL;

        if ($directory === 'config' OR $directory === 'i18n') {
            // Search in reverse, for merging
            $paths = array_reverse($paths);

            foreach ($paths as $path) {
                if (static::isFile($path . $search)) {
                    // A matching file has been found
                    $found[] = $path . $search;
                }
            }
        } else {

            foreach ($paths as $path) {
                if (static::isFile($path . $search)) {
                    // A matching file has been found
                    $found = $path . $search;

                    // Stop searching
                    break;
                }
            }
        }

        if ($found === NULL) {
            if ($required === TRUE) {
                // If the file is required, throw an exception
                $lang = static::lang('core.resource_not_found', [':directory' => $directory, ':filename' => $filename]);
                throw new CException($lang);
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
     * Fetch an i18n language item.
     *
     * @param   string  language key to fetch
     * @param   array   additional information to insert into the line
     * @return  string  i18n language string, or the requested key if the i18n item is not found
     */
    public static function lang($key = null, array $args = array(), $locale = null) {
        if ($key == null) {
            return CTranslation::translator();
        }

        return CTranslation::translator()->trans($key, $args);
    }

    /**
     * Fetch an i18n language item.
     *
     * @param   string  language key to fetch
     * @param   array   additional information to insert into the line
     * @return  string  i18n language string, or the requested key if the i18n item is not found
     */
    public static function trans($key = null, array $args = array()) {
        static::lang($key, $args);
    }

    /**
     * Checks if given data is file, handles mixed input
     *
     * @param  mixed $value
     * @return boolean
     */
    private static function isFile($value) {
        $value = strval(str_replace("\0", "", $value));

        return is_file($value);
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
        return c::classUsesRecursive($class);
    }

    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  string  $trait
     * @return array
     */
    public static function traitUsesRecursive($trait) {
        return c::traitUsesRecursive($trait);
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function value($value) {
        return c::value($value);
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
     * 
     * @return string
     */
    public static function version() {
        return CF_VERSION;
    }

    /**
     * 
     * @return string
     */
    public static function codeName() {
        return CF_CODENAME;
    }

    /**
     * 
     * @param string $domain
     * @return string
     */
    public static function appPath($domain = null) {

        $appCode = static::appCode($domain);

        return DOCROOT . 'application/' . $appCode . '/';
    }

    public static function currentController() {
        return CHTTP::kernel()->controller();
    }

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public static function getLocale() {

        return CF::config('app.locale');
    }

    public static function setLocale($locale) {
        /*
          CF::config('app')->set('locale', $locale);

          CTranslation::translator()->setLocale($locale);

          CEvent::dispatch('cf.locale.updated');
         * 
         */
        //$this['events']->dispatch(new CBase_Events_LocaleUpdated($locale));
    }

    public static function appDir($appCode = null) {
        if ($appCode == null) {
            $appCode = static::appCode();
        }
        return DOCROOT . 'application' . DS . $appCode;
    }

    public static function isDevSuite() {
        return cstr::endsWith(CF::domain(), '.test');
    }

    public static function isTesting() {
        //TODO: this should be true when CF is running in phpunit
        return false;
    }

}

// End CF
