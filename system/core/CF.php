<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * CF Class.
 * This class is core of Cresenity Framework loaded in Bootstrap.php.
 */
final class CF {
    use CFDeprecatedTrait;

    const CFCLI_CURRENT_DOMAIN_FILE = DOCROOT . 'data' . DS . 'current-domain';

    const CFCLI_CURRENT_APPCODE_FILE = DOCROOT . 'data' . DS . 'current-app';

    // Security check that is added to all generated PHP files
    const FILE_SECURITY = '<?php defined(\'SYSPATH\') OR die(\'No direct script access.\');';

    // The singleton instance of the controller (last of the controller)

    /**
     * @var null
     *
     * @deprecated since 1.2, use CF::controller()
     */
    public static $instance;

    /**
     * Chartset used for this application.
     *
     * @var string
     */
    public static $charset = 'utf-8';

    /**
     * Logger Instance.
     *
     * @var CLogger logging object
     */
    public static $logger;

    /**
     * The current locale.
     *
     * @var string
     */
    private static $locale;

    /**
     * The fallback locale.
     *
     * @var string
     */
    private static $fallbackLocale;

    /**
     *  Internal caches for faster loading.
     *
     * @var array
     */
    private static $internalCache = [];

    /**
     * CF Data domain.
     *
     * @var array
     */
    private static $data;

    /**
     * List of Shared appCode used for CF.
     *
     * @var array
     */
    private static $sharedAppCode = [];

    /**
     * CF Session.
     *
     * @var CSession_Store
     */
    private static $session;

    /**
     * Check CF is running on production.
     *
     * @return bool
     */
    public static function isProduction() {
        return static::environment() === CBase::ENVIRONMENT_PRODUCTION;
    }

    public static function environment() {
        if (defined('IN_PRODUCTION') && IN_PRODUCTION) {
            return CBase::ENVIRONMENT_PRODUCTION;
        }

        return CF::config('app.environment', CBase::ENVIRONMENT_DEVELOPMENT);
    }

    /**
     * Check given domain exists or not.
     *
     * @param string $domain domain to check
     *
     * @return bool
     */
    public static function domainExists($domain) {
        return CFData::domain($domain) !== null;
    }

    /**
     * Create domain.
     *
     * @param string $domain     asd
     * @param array  $domainData asd
     *
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
     *
     * @see http://www.php.net/globals
     *
     * @return void
     */
    public static function setup() {
        static $run;

        // This function can only be run once
        if ($run === true) {
            return;
        }

        // Start the environment setup benchmark
        CFBenchmark::start(SYSTEM_BENCHMARK . '_environment_setup');

        // Define CF error constant
        define('E_CF', 42);

        // Define 404 error constant
        define('E_PAGE_NOT_FOUND', 43);

        // Define database error constant
        define('E_DATABASE_ERROR', 44);

        // Set autoloader
        spl_autoload_register(['CF', 'autoLoad']);

        // Set and test the logger instance, we need to know whats wrong when CF Fail
        self::$logger = CLogger::instance();

        // Disable notices and "strict" errors
        $ER = error_reporting(~E_NOTICE & ~E_STRICT);

        if (function_exists('date_default_timezone_set')) {
            $timezone = self::config('app.timezone');

            // Set default timezone, due to increased validation of date settings
            // which cause massive amounts of E_NOTICEs to be generated in PHP 5.2+
            date_default_timezone_set(empty($timezone) ? date_default_timezone_get() : $timezone);
        }

        // Restore error reporting
        error_reporting($ER);

        // Send default text/html UTF-8 header
        //header('Content-Type: text/html; charset=UTF-8');

        // Load locales
        $locale = self::config('app.locale');

        // Set locale information
        self::$locale = setlocale(LC_ALL, $locale);
        // Set locale information
        self::$fallbackLocale = self::config('app.fallback_locale');

        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_setup');
        static::loadBootstrapFiles();
        // Setup is complete, prevent it from being run again
        $run = true;
        // Stop the environment setup routine
    }

    /**
     * Load all bootstrap files.
     *
     * @return void
     */
    private static function loadBootstrapFiles() {
        CFBenchmark::start(SYSTEM_BENCHMARK . '_environment_bootstrap');

        CFBenchmark::start(SYSTEM_BENCHMARK . '_environment_system_bootstrap');
        $bootstrapPath = DOCROOT . 'system' . DS;
        if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
            include $bootstrapPath . 'bootstrap' . EXT;
        }
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_system_bootstrap');
        //try to locate bootstrap files for modules
        CFBenchmark::start(SYSTEM_BENCHMARK . '_environment_module_bootstrap');

        foreach (CF::modules() as $module) {
            $bootstrapPath = DOCROOT . 'modules' . DS . $module . DS;
            if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
                include $bootstrapPath . 'bootstrap' . EXT;
            }
        }

        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_module_bootstrap');

        //try to locate bootstrap files for application
        CFBenchmark::start(SYSTEM_BENCHMARK . '_environment_application_bootstrap');
        $bootstrapPath = DOCROOT . 'application' . DS . CF::appCode() . DS;

        if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
            include $bootstrapPath . 'bootstrap' . EXT;
        }
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_application_bootstrap');

        //try to locate bootstrap files for org
        if (strlen(CF::orgCode()) > 0) {
            $bootstrapPath .= CF::orgCode() . DS;
            if (file_exists($bootstrapPath . 'bootstrap' . EXT)) {
                include $bootstrapPath . 'bootstrap' . EXT;
            }
        }
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_environment_bootstrap');
    }

    /**
     * Invoke.
     *
     * @param mixed $uri
     *
     * @return void
     */
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

        if ($controller instanceof \Symfony\Component\HttpFoundation\Response) {
            return $controller;
        }
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

        if (isset($class)
            && ($class->isAbstract()
            || (IN_PRODUCTION && $class->getConstant('ALLOW_PRODUCTION') == false))
        ) {
            // Controller is not allowed to run in production
            throw new Exception(c::__(
                'class is abstract or not allowed in production in :class_name',
                [':class_name' => $class_name]
            ));
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
                throw new Exception(c::__(
                    'method :method is hidden methods in :class_name',
                    [':method' => $method, ':class_name' => $class_name]
                ));
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
            $arguments = [$method, $arguments];
        }

        // Execute the controller method
        return $method->invokeArgs($controller, $arguments);
    }

    /**
     * Displays a 404 page.
     *
     * @param string $page     URI of page
     * @param string $template custom template
     *
     * @throws CHTTP_Exception_NotFoundHttpException
     *
     * @return void
     */
    public static function show404($page = false, $template = false) {
        return c::abort(404);
    }

    /**
     * @param type $directory
     * @param type $domain
     *
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
     * @param string $directory
     * @param string $domain
     * @param mixed  $withShared
     *
     * @return array array of directory
     */
    public static function getDirs($directory, $domain = null, $withShared = true) {
        $includePaths = CF::paths($domain, false, $withShared);
        $dirs = [];
        foreach ($includePaths as $p) {
            $path = $p . $directory . DS;
            if (is_dir($path)) {
                $dirs[] = $path;
            }
        }

        return $dirs;
    }

    /**
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @param mixed  $force_reload
     *
     * @return string[]
     */
    public static function getFiles($directory, $filename, $domain = null, $force_reload = false) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        $paths = CF::paths($domain, $force_reload);

        $result = [];
        foreach ($paths as $path) {
            if (file_exists($path . $directory . DS . $filename . EXT)) {
                $result[] = $path . $directory . DS . $filename . EXT;
            }
        }

        return $result;
    }

    /**
     * @param string $directory
     * @param string $filename
     * @param string $domain
     *
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
     * @param null|mixed $domain
     * @param bool       $forceReload
     * @param mixed      $withShared
     *
     * @return array
     */
    public static function paths($domain = null, $forceReload = false, $withShared = true) {
        if ($domain == null) {
            $domain = CF::domain($domain);
        }
        $isDiffAppCode = false;
        if (CF::appCode() != CF::appCode($domain)) {
            $isDiffAppCode = true;
        }
        if (CF::isTesting() || $isDiffAppCode) {
            $forceReload = true;
        }

        $cacheKey = 'paths.' . $domain . '.' . ($withShared ? 'withShared' : 'withoutShared');
        $paths = null;
        if (!$forceReload) {
            $paths = static::getInternalCache($cacheKey);
        }
        if ($paths === null) {
            //we try to search all paths for this domain
            $paths = [];
            $orgCode = CF::orgCode($domain);
            $appCode = $isDiffAppCode ? CF::appCode() : CF::appCode($domain);

            $modules = CF::modules($domain);
            //when this domain is org
            if (strlen($orgCode) > 0) {
                $paths[] = APPPATH . $appCode . DS . $orgCode . DS;
            }
            if (strlen($appCode) > 0) {
                //add theme path if theme exists
                $paths[] = APPPATH . $appCode . DS . 'default' . DS;
            }
            if ($withShared) {
                $sharedAppCode = CF::getSharedApp($domain);
                foreach ($sharedAppCode as $key => $value) {
                    if (strlen($orgCode) > 0) {
                        //add theme path if theme exists
                        $paths[] = APPPATH . $value . DS . $orgCode . DS;
                    }
                    $paths[] = APPPATH . $value . DS . 'default' . DS;
                }
            }

            foreach ($modules as $module) {
                $paths[] = MODPATH . $module . DS;
            }
            $paths[] = SYSPATH;
            $paths[] = DOCROOT;
            static::setInternalCache($cacheKey, $paths);
        }

        return $paths;
    }

    /**
     * Get a config item or group.
     *
     * @param mixed      $group
     * @param null|mixed $default
     * @param mixed      $required
     *
     * @return CConfig|mixed
     */
    public static function config($group, $default = null, $required = true) {
        $path = null;
        if (strpos($group, '.') !== false) {
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
     * @param string $level
     * @param string $message
     *
     * @return void
     */
    public static function log($level, $message) {
        if (class_exists('CLogger')) {
            CLogger::instance()->add($level, $message);
        }
    }

    /**
     * Provides class auto-loading.
     *
     * @param string $class
     * @param string $directory
     *
     * @return bool
     */
    public static function autoLoad($class, $directory = 'libraries') {
        if (class_exists($class, false)) {
            return true;
        }
        if (($prefix = strpos($class, '_')) > 0) {
            // Find the class suffix
            $prefix = substr($class, 0, $prefix);
        } else {
            // No suffix
            $prefix = false;
        }
        if (($suffix = strrpos($class, '_')) > 0) {
            // Find the class suffix
            $suffix = substr($class, $suffix + 1);
        } else {
            // No suffix
            $suffix = false;
        }

        if ($suffix === 'Controller' || $prefix === 'Controller') {
            $type = 'controllers';
            $directory = 'controllers';
            // Lowercase filename

            $file = substr($class, 0, -11);
            if ($prefix) {
                $file = substr($class, 11);
            }
            //$file = str_replace('_', DS, $file);
            $file = implode(DS, array_map(function ($item) {
                return lcfirst($item);
            }, explode('_', $file)));
        } else {
            // This could be either a library or a helper, but libraries must
            // always be capitalized, so we check if the first character is
            // uppercase. If it is, we are loading a library, not a helper.
            $type = ($class[0] < 'a') ? 'libraries' : 'helpers';
            $file = $class;
        }

        $classNotFound = false;
        if ($type == 'controllers') {
            if ($filename = self::findFile($type, $file, false, false, false, false)) {
                require $filename;

                return true;
            } else {
                $type = 'libraries';
                $directory = 'libraries';
                $file = $class;
            }
        }

        if ($filename = self::findFile($type, $file)) {
            require $filename;
            $classNotFound = true;

            return true;
        }

        if (!$classNotFound) {
            // Transform the class name according to PSR-0
            $routing_class = ltrim($class, '\\');
            $routingFile = '';
            $namespace = '';

            $is_namespace = false;
            if ($last_namespace_position = strripos($routing_class, '\\')) {
                $is_namespace = true;
                $namespace = substr($routing_class, 0, $last_namespace_position);

                $routing_class = substr($routing_class, $last_namespace_position + 1);
                $routingFile = str_replace('\\', DS, $namespace) . DS;
            }

            $routingFile .= str_replace('_', DS, $routing_class);

            if (substr($routingFile, strlen($routingFile) - 1, 1) == DS) {
                $routingFile = substr($routingFile, 0, strlen($routingFile) - 1) . '_';
            }

            if ($directory == 'libraries') {
                // find file at vendor first
                if ($path = self::findFile('vendor', $routingFile)) {
                    // Load the class file

                    require $path;

                    if (class_exists($class) || interface_exists($class)) {
                        $classNotFound = false;

                        return true;
                    }
                }
            }

            if ($directory == 'libraries') {
                if (static::isTesting()) {
                    if ($path = self::findFile('tests', $routingFile)) {
                        // Load the class file

                        require $path;

                        if (class_exists($class) || interface_exists($class)) {
                            $classNotFound = false;

                            return true;
                        }
                    }
                }
            }

            // find file at libraries
            if ($path = self::findFile($directory, $routingFile)) {
                // Load the class file
                require $path;
                $classNotFound = true;

                return true;
            }

            // check route file at helpers
            if (!$classNotFound) {
                $tempRoutingFile = explode(DS, $routingFile);
                if (strtolower($tempRoutingFile[0]) == 'helpers') {
                    $tempRoutingFile[0] = 'helpers';
                    $routingFile = str_replace('Helpers' . DS, '', $routingFile);
                    $directory = 'helpers';
                    if ($path = self::findFile($directory, $routingFile)) {
                        // Load the class file

                        require $path;
                        $classNotFound = true;

                        return true;
                    }
                }
            }
        }

        if (!$classNotFound) {
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

            return false;
        }

        if ($filename = self::findFile($type, $class)) {
            // Load the class extension
            require $filename;
        } elseif ($suffix !== 'Core' and class_exists($class . '_Core', false)) {
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

        return true;
    }

    /**
     * Detect CF is running on console in cf command or not.
     *
     * @return bool
     */
    public static function isCFCli() {
        return defined('CFCLI');
    }

    /**
     * Detect CF is running on console or not.
     *
     * @return type
     */
    public static function isCli() {
        return PHP_SAPI === 'cli';
    }

    /**
     * To get cliDomain.
     *
     * @return string
     */
    public static function cliDomain() {
        $domain = null;
        if (file_exists(static::CFCLI_CURRENT_DOMAIN_FILE)) {
            $domain = trim(file_get_contents(static::CFCLI_CURRENT_DOMAIN_FILE));
        }

        return $domain;
    }

    /**
     * To get cliAppCode.
     *
     * @return string
     */
    public static function cliAppCode() {
        $domain = null;
        if (file_exists(static::CFCLI_CURRENT_APPCODE_FILE)) {
            $domain = trim(file_get_contents(static::CFCLI_CURRENT_APPCODE_FILE));
        }

        return $domain;
    }

    public static function domain() {
        $domain = '';
        if (static::isCli() || static::isCFCli()) {
            // Command line requires a bit of hacking
            if (static::isCFCli() || static::isTesting()) {
                $domain = static::cliDomain();
            } else {
                if (isset($_SERVER['argv'][2])) {
                    $domain = $_SERVER['argv'][2];
                }
            }
        } else {
            if (isset($_SERVER['SERVER_NAME'])) {
                $domain = $_SERVER['SERVER_NAME'];
            }
            if (strlen($domain) == 0) {
                if (isset($_SERVER['HTTP_HOST'])) {
                    $domain = $_SERVER['HTTP_HOST'];
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
     * @param mixed $directory  directory to search in
     * @param mixed $filename   filename to look for (without extension)
     * @param mixed $required   file required
     * @param mixed $ext        file extension
     * @param mixed $reload
     * @param mixed $withShared
     *
     * @throws Exception if file is required and not found
     *
     * @return array|string|bool if the type is config, i18n or l10n,
     */
    public static function findFile($directory, $filename, $required = false, $ext = false, $reload = false, $withShared = true) {
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
        // Nothing found, yet
        $found = null;
        $cacheKey = 'find_file_paths.' . $search . '.' . ($withShared ? 'withShared' : 'withoutShared');
        if (!$reload) {
            $found = static::getInternalCache($cacheKey);
        }

        if ($found === null) {
            // Load include paths
            $paths = self::paths(null, $reload, $withShared);

            if ($directory === 'config' or $directory === 'i18n') {
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

            if ($found === null) {
                if ($required === true) {
                    // If the file is required, throw an exception
                    throw new Exception(c::__('core.resource_not_found', [':directory' => $directory, ':filename' => $filename]));
                } else {
                    // Nothing was found, return FALSE
                    $found = false;
                }
            }

            static::setInternalCache($cacheKey, $found);
        }

        return $found;
    }

    /**
     * Checks if given data is file, handles mixed input.
     *
     * @param mixed $value
     *
     * @return bool
     */
    private static function isFile($value) {
        $value = strval(str_replace("\0", '', $value));

        return is_file($value);
    }

    /**
     * Get data domain.
     *
     * @param string $domain
     *
     * @return array
     */
    public static function data($domain = null) {
        $domain = $domain == null ? self::domain() : $domain;

        if (!isset(self::$data[$domain])) {
            self::$data[$domain] = CFData::domain($domain);
            if (self::$data[$domain] == null) {
                //try to locate wildcard subdomain
                $wildcardDomain = implode('.', ['$'] + array_slice(explode('.', $domain), 0));
                self::$data[$domain] = CFData::domain($wildcardDomain);
            }
        }

        return self::$data[$domain];
    }

    /**
     * Get application id for domain.
     *
     * @param null|mixed $domain
     *
     * @return string
     */
    public static function appId($domain = null) {
        $data = self::data($domain);

        return isset($data['app_id']) ? $data['app_id'] : null;
    }

    /**
     * Get application code for domain.
     *
     * @param null|mixed $domain
     *
     * @return string
     */
    public static function appCode($domain = null) {
        if (CF::isCFCli() || CF::isTesting()) {
            if (CF::cliAppCode()) {
                return CF::cliAppCode();
            }
        }
        $data = self::data($domain);

        return isset($data['app_code']) ? $data['app_code'] : null;
    }

    /**
     * Get org id for domain.
     *
     * @param string $domain
     *
     * @return int
     */
    public static function orgId($domain = null) {
        $data = self::data($domain);

        return isset($data['org_id']) ? $data['org_id'] : null;
    }

    /**
     * Get org code for this domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public static function orgCode($domain = null) {
        $data = self::data($domain);

        return isset($data['org_code']) ? $data['org_code'] : null;
    }

    /**
     * Add Shared App in runtime.
     *
     * @param string $appCode
     */
    public static function addSharedApp($appCode) {
        if (!in_array($appCode, self::$sharedAppCode)) {
            self::$sharedAppCode[] = $appCode;
            //clear all internal cache
            static::clearInternalCache();
        }
    }

    /**
     * Get shared application code for this domain.
     *
     * @param string $domain
     *
     * @return array
     */
    public static function getSharedApp($domain = null) {
        $data = self::data($domain);
        if (!isset($data['shared_app_code'])) {
            $data['shared_app_code'] = [];
        }

        $data['shared_app_code'] = array_merge($data['shared_app_code'], self::$sharedAppCode);

        return isset($data['shared_app_code']) ? $data['shared_app_code'] : [];
    }

    /**
     * Get theme for this domain.
     *
     * @param null|mixed $domain
     *
     * @return array
     *
     * @deprecated since 1.2
     */
    public static function theme($domain = null) {
        $data = self::data($domain);

        return isset($data['theme']) ? $data['theme'] : null;
    }

    /**
     * Get modules for this domain.
     *
     * @param null|mixed $domain
     *
     * @return array
     */
    public static function modules($domain = null) {
        $data = self::data($domain);

        return isset($data['modules']) ? $data['modules'] : ['cresenity'];
    }

    /**
     * @return string
     */
    public static function version() {
        return CF_VERSION;
    }

    /**
     * @return string
     */
    public static function codeName() {
        return CF_CODENAME;
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    public static function appPath($domain = null) {
        $appCode = static::appCode($domain);

        return DOCROOT . 'application/' . $appCode . '/';
    }

    /**
     * Get current running controller.
     *
     * @return CController
     */
    public static function currentController() {
        return CHTTP::kernel()->controller();
    }

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public static function getLocale() {
        return static::$locale;
    }

    /**
     * Get the current application charset.
     *
     * @return string
     */
    public static function getCharset() {
        return static::$charset;
    }

    /**
     * Get the current application fallback locale.
     *
     * @return string
     */
    public static function getFallbackLocale() {
        return static::$fallbackLocale;
    }

    /**
     * Set the current application locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public static function setLocale($locale) {
        static::$locale = $locale;
        CTranslation::translator()->setLocale($locale);
        CEvent::dispatch('cf.locale.updated');
    }

    /**
     * Set the current application fallback locale.
     *
     * @param string $fallbackLocale
     *
     * @return void
     */
    public function setFallbackLocale($fallbackLocale) {
        // static::$fallbackLocale = $fallbackLocale;
        // CTranslation::translator()->setFallback($locale);
    }

    /**
     * Get current application directory.
     *
     * @param null|string $appCode
     *
     * @return bool
     */
    public static function appDir($appCode = null) {
        if ($appCode == null) {
            $appCode = static::appCode();
        }

        return DOCROOT . 'application' . DS . $appCode;
    }

    /**
     * Check if CF is run under devsuite.
     *
     * @return bool
     */
    public static function isDevSuite() {
        return cstr::endsWith(CF::domain(), '.test');
    }

    /**
     * Check if CF is run under testing.
     *
     * @return bool
     */
    public static function isTesting() {
        if (defined('CFTesting')
            || (is_array($_SERVER) && isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] == 'testing')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return all app availables.
     *
     * @return array
     */
    public static function getAvailableAppCode() {
        $path = DOCROOT . 'application';
        $directories = CFile::directories($path);

        return c::collect($directories)->map(function ($v) {
            return basename($v);
        })->all();
    }

    /**
     * Check appCode is exits on directory.
     *
     * @param mixed $appCode
     *
     * @return bool
     */
    public static function appCodeExists($appCode) {
        return in_array($appCode, static::getAvailableAppCode());
    }

    /**
     * Get CF internal cache.
     *
     * @param string     $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    private static function getInternalCache($key, $default = null) {
        if (isset(static::$internalCache[$key])) {
            return static::$internalCache[$key];
        }

        return $default;
    }

    /**
     * Set CF internal cache.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    private static function setInternalCache($key, $value) {
        static::$internalCache[$key] = $value;
    }

    /**
     * Clear CF internal cache.
     *
     * @return void
     */
    private static function clearInternalCache() {
        static::$internalCache = [];
    }

    public static function isDownForMaintenance() {
        $file = CF::findFile('data', 'down');

        if ($file != null) {
            $data = @include $file;
            $viewName = 'system.maintenance';
            $down = false;
            if (is_array($data)) {
                $down = carr::get($data, 'down', true);
                if ($down) {
                    $request = CHTTP::request();

                    if (isset($request->cookie()[carr::get($data, 'cookie', '')])) {
                        return false;
                    }
                    $viewName = carr::get($data, 'view', $viewName);
                }
            }

            if ($down) {
                return c::response()->view($viewName, ['data' => $data], 503);
            }
        }

        return false;
    }

    public static function asAppCode($appCode, $callback) {
        if (is_callable($callback)) {
            $domain = CF::domain();
            $originalAppCode = static::appCode();
            if ($originalAppCode) {
                static::$data[$domain]['app_code'] = $appCode;
                $callback();
                static::$data[$domain]['app_code'] = $originalAppCode;
            }
        }
    }

    public static function session() {
        if (static::$session == null && CSession::sessionConfigured()) {
            $request = CHTTP::request();
            CSession::manager()->applyNativeSession();

            static::$session = c::tap(CSession::manager()->createStore(), function ($session) use ($request) {
                $session->setId($request->cookies->get($session->getName()));
                $session->setRequestOnHandler($request);
                $session->start();
            });
        }

        return static::$session;
    }
}
