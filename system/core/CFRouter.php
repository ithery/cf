<?php

defined('SYSPATH') OR die('No direct access allowed.');

use Symfony\Component\Routing\Route as SymfonyRoute;

class CFRouter {

    protected static $routesRuntime = array();
    protected static $routes;
    public static $current_uri = '';
    public static $query_string = '';
    public static $complete_uri = '';
    public static $routed_uri = '';
    public static $url_suffix = '';
    public static $segments;
    public static $rsegments;
    public static $controller;
    public static $controller_dir;
    public static $controller_dir_ucfirst;
    public static $controller_path;
    public static $method = 'index';
    public static $arguments = array();
    public static $routeData = array();

    /**
     * CFRouter setup routine. Automatically called during CF setup process.
     *
     * @return  void
     */
    public static function setup() {
        self::resetup(self::$current_uri);

        // Last chance to set routing before a 404 is triggered
        CFEvent::run('system.post_routing');

        if (self::$controller === NULL) {
            // No controller was found, so no page can be rendered
            CFEvent::run('system.404');
        }
    }

    /**
     * CFRouter get route data
     *
     * @return  array
     */
    public static function getRouteData($uri = null) {
        if (self::$routeData == null) {
            self::$routeData = array();
        }

        $currentUri = NULL;
        $routes = NULL;
        if ($uri !== null) {
            $currentUri = $uri;
        }

        if ($currentUri === null) {
            $currentUri = self::getUri();
        }

        // Load routes
        $routesConfig = CF::config('routes');
        $routesRuntime = self::$routesRuntime;
        $routes = array_merge($routesConfig, $routesRuntime);

        // Default route status
        $default_route = FALSE;

        if ($currentUri === '') {
            // Make sure the default route is set
            if (!isset($routes['_default']))
                throw new CException('Please set a default route in config/routes.php');

            // Use the default route when no segments exist
            $currentUri = $routes['_default'];

            // Default route is in use
            $default_route = TRUE;
        }

        // Make sure the URL is not tainted with HTML characters
        $currentUri = chtml::specialchars($currentUri, FALSE);

        // Remove all dot-paths from the URI, they are not valid
        $currentUri = preg_replace('#\.[\s./]*/#', '', $currentUri);


        if (!isset(self::$routeData[$currentUri])) {
            $data = array();
            $data['routesConfig'] = $routesConfig;
            $data['routesRuntime'] = $routesRuntime;
            $data['routes'] = $routes;
            $data['current_uri'] = $currentUri;
            $data['query_string'] = '';
            $data['complete_uri'] = '';
            $data['routed_uri'] = '';
            $data['url_suffix'] = '';
            $data['segments'] = NULL;
            $data['rsegments'] = NULL;
            $data['controller'] = NULL;
            $data['controller_dir'] = NULL;
            $data['controller_dir_ucfirst'] = NULL;
            $data['controller_path'] = NULL;
            $data['method'] = 'index';
            $data['arguments'] = array();


            if (!empty($_SERVER['QUERY_STRING'])) {
                // Set the query string to the current query string
                $data['query_string'] = '?' . trim($_SERVER['QUERY_STRING'], '&/');
            }

            // At this point segments, rsegments, and current URI are all the same
            $data['segments'] = $data['rsegments'] = $data['current_uri'] = trim($data['current_uri'], '/');

            // Set the complete URI
            $data['complete_uri'] = $data['current_uri'] . $data['query_string'];

            // Explode the segments by slashes
            $data['segments'] = ($default_route === TRUE OR $data['segments'] === '') ? array() : explode('/', $data['segments']);

            if ($default_route === FALSE AND count($data['routes']) > 1) {
                // Custom routing
                $data['rsegments'] = self::routedUri($data['current_uri'], $data['routes']);
            }

            // The routed URI is now complete
            $data['routed_uri'] = $data['rsegments'];

            // Routed segments will never be empty
            $data['rsegments'] = explode('/', $data['rsegments']);

            // Prepare to find the controller
            $controller_path = '';
            $controller_path_ucfirst = '';
            $c_dir = '';
            $c_dir_ucfirst = '';
            $method_segment = NULL;

            // Paths to search
            $paths = CF::paths();

            foreach ($data['rsegments'] as $key => $segment) {
                // Add the segment to the search path
                $c_dir = $controller_path;
                $c_dir_ucfirst = strtolower($controller_path_ucfirst);
                $controller_path .= $segment;
                $controller_path_ucfirst .= ucfirst($segment);
                $found = FALSE;

                foreach ($paths as $dir) {
                    // Search within controllers only
                    $dir .= 'controllers' . DS;

                    if (is_dir($dir . $controller_path) OR is_file($dir . $controller_path . EXT)) {

                        // Valid path
                        $found = TRUE;

                        // The controller must be a file that exists with the search path
                        if ($c = str_replace('\\', '/', realpath($dir . $controller_path . EXT))
                                AND is_file($c)) {

                            // Set controller name
                            $data['controller'] = $segment;

                            // Set controller dir
                            $data['controller_dir'] = $c_dir;
                            $data['controller_dir_ucfirst'] = $c_dir_ucfirst;

                            // Change controller path
                            $data['controller_path'] = $c;

                            // Set the method segment
                            $method_segment = $key + 1;

                            // Stop searching
                            break;
                        }
                        //if(strlen($c_dir)>0) $c_dir.=DS;
                    }

                    //                                echo $c_dir .'<br/>';
                }

                if ($found === FALSE) {
                    // Maximum depth has been reached, stop searching
                    break;
                }

                // Add another slash
                $controller_path .= '/';
                $controller_path_ucfirst .= '/';
            }

            if ($method_segment !== NULL AND isset($data['rsegments'][$method_segment])) {
                // Set method
                $data['method'] = $data['rsegments'][$method_segment];

                if (isset($data['rsegments'][$method_segment + 1])) {
                    // Set arguments
                    $data['arguments'] = array_slice($data['rsegments'], $method_segment + 1);
                }
            }
            self::$routeData[$currentUri] = $data;
        }
        return self::$routeData[$currentUri];
    }

    /**
     * 
     * @param string $uri if null, using the current uri 
     * 
     * @return void
     */
    public static function resetup($uri = null) {
        if ($uri !== null) {
            self::$current_uri = $uri;
        }
        if (strlen(self::$current_uri) == 0) {
            self::findUri();
        }
        $data = self::getRouteData(self::$current_uri);

        self::$routes = carr::get($data, 'routes');
        self::$current_uri = carr::get($data, 'current_uri');
        self::$query_string = carr::get($data, 'query_string');
        self::$complete_uri = carr::get($data, 'complete_uri');
        self::$routed_uri = carr::get($data, 'routed_uri');
        self::$url_suffix = carr::get($data, 'url_suffix');
        self::$segments = carr::get($data, 'segments');
        self::$rsegments = carr::get($data, 'rsegments');
        self::$controller = carr::get($data, 'controller');
        self::$controller_dir = carr::get($data, 'controller_dir');
        self::$controller_dir_ucfirst = carr::get($data, 'controller_dir_ucfirst');
        self::$controller_path = carr::get($data, 'controller_path');
        self::$method = carr::get($data, 'method');
        self::$arguments = carr::get($data, 'arguments');
    }

    /**
     * 
     * Attempts to determine the current URI using CLI, GET, PATH_INFO, ORIG_PATH_INFO, REQUEST_URI or PHP_SELF.
     * 
     * @return string uri
     */
    public static function getUri() {
        $currentUri = '';
        if (PHP_SAPI === 'cli') {
            if (defined('CFCLI')) {
                CFConsole::execute();
            } else {
                // Command line requires a bit of hacking
                if (isset($_SERVER['argv'][1])) {
                    $currentUri = $_SERVER['argv'][1];

                    // Remove GET string from segments
                    if (($query = strpos($currentUri, '?')) !== FALSE) {
                        list ($currentUri, $query) = explode('?', $currentUri, 2);

                        // Parse the query string into $_GET
                        parse_str($query, $_GET);

                        // Convert $_GET to UTF-8
                        $_GET = utf8::clean($_GET);
                    }
                }
            }
        } elseif (isset($_GET['cfUri'])) {

            // Use the URI defined in the query string
            $currentUri = $_GET['cfUri'];

            // Remove the URI from $_GET
            unset($_GET['cfUri']);

            // Remove the URI from $_SERVER['QUERY_STRING']
            $_SERVER['QUERY_STRING'] = preg_replace('~\cfUri\b[^&]*+&?~', '', $_SERVER['QUERY_STRING']);
        } elseif (isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO']) {
            $currentUri = $_SERVER['PATH_INFO'];
            if ($currentUri == '/404.shtml' || $currentUri == '404.shtml') {
                $currentUri = $_SERVER['REQUEST_URI'];
            }
            if ($currentUri == '/403.shtml') {
                $currentUri = $_SERVER['REQUEST_URI'];
            }
            if ($currentUri == '/500.shtml') {
                if (isset($_SERVER['REDIRECT_REDIRECT_SCRIPT_URL'])) {
                    $currentUri = $_SERVER['REDIRECT_REDIRECT_SCRIPT_URL'];
                }
                if (isset($_SERVER['REDIRECT_REDIRECT_REDIRECT_QUERY_STRING'])) {
                    $_SERVER['QUERY_STRING'] = $_SERVER['REDIRECT_REDIRECT_REDIRECT_QUERY_STRING'];
                }
                if (isset($_SERVER['REDIRECT_REDIRECT_QUERY_STRING'])) {
                    $_SERVER['QUERY_STRING'] = $_SERVER['REDIRECT_REDIRECT_QUERY_STRING'];
                }
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO']) AND $_SERVER['ORIG_PATH_INFO'] AND $_SERVER['ORIG_PATH_INFO'] != '/403.shtml') {
            $currentUri = $_SERVER['ORIG_PATH_INFO'];
        } elseif (isset($_SERVER['REQUEST_URI']) AND $_SERVER['REQUEST_URI']) {
            $currentUri = $_SERVER['REQUEST_URI'];
            $currentUri = strtok($currentUri, '?');
        } elseif (isset($_SERVER['PHP_SELF']) AND $_SERVER['PHP_SELF']) {
            $currentUri = $_SERVER['PHP_SELF'];
        }

        if (($strpos_fc = strpos($currentUri, KOHANA)) !== FALSE) {
            // Remove the front controller from the current uri
            $currentUri = (string) substr($currentUri, $strpos_fc + strlen(KOHANA));
        }

        // Remove slashes from the start and end of the URI
        $currentUri = trim($currentUri, '/');

        if ($currentUri !== '') {
            if ($suffix = CF::config('core.url_suffix') AND strpos($currentUri, $suffix) !== FALSE) {
                // Remove the URL suffix
                $currentUri = preg_replace('#' . preg_quote($suffix) . '$#u', '', $currentUri);

                // Set the URL suffix
                self::$url_suffix = $suffix;
            }

            // Reduce multiple slashes into single slashes
            $currentUri = preg_replace('#//+#', '/', $currentUri);
        }

        return $currentUri;
    }

    /**
     * Attempts to determine the current URI using CLI, GET, PATH_INFO, ORIG_PATH_INFO, or PHP_SELF.
     *
     * @return  void
     */
    public static function findUri() {
        self::$current_uri = self::getUri();
        return self::$current_uri;
    }

    public static function getRoutes() {
        $routesConfig = CF::config('routes');
        $routesRuntime = self::$routesRuntime;
        return array_merge($routesConfig, $routesRuntime);
    }

    public static function routed_uri($uri, & $routes = null) {
        return static::routedUri($uri, $routes);
    }

    /**
     * Generates routed URI from given URI.
     *
     * @param  string  URI to convert
     * @return string  Routed uri
     */
    public static function routedUri($uri, & $routes = null) {
        if ($routes === NULL) {
            // Load routes
            $routes = self::getRoutes();
        }



        // Prepare variables
        $routedUri = $uri = trim($uri, '/');

        if (isset($routes[$uri])) {
            // Literal match, no need for regex
            $routedUri = $routes[$uri];
        } else {
            // Loop through the routes and see if anything matches
            foreach ($routes as $key => $val) {
                if ($key === '_default')
                    continue;
                if (is_callable($val)) {
                    preg_match_all("/{([\w]*)}/", $key, $matches, PREG_SET_ORDER);
                    $callbackArgs = array($uri);
                    foreach ($matches as $matchedVal) {
                        $str = $matchedVal[1]; //matches str without bracket {}
                        $bStr = $matchedVal[0]; //matches str with bracket {}
                        $bracketKeys[] = null;
                        $key = str_replace($bStr, '(.+?)', $key);
                    }


                    $matchesBracket = false;
                    $key = str_replace("/", "\/", $key);
                    preg_match('#' . $key . '#ims', $uri, $matches);

                    if (preg_match('#' . $key . '#ims', $uri, $matches)) {

                        $matchesBracket = array_slice($matches, 1);
                    }
                    $matchesBracket ? $callbackArgs = array_merge($callbackArgs, $matchesBracket) : $callbackArgs = array_merge($callbackArgs, $bracketKeys);
                    $val = call_user_func_array($val, $callbackArgs);

                    if ($val == null) {
                        continue;
                    }
                }

                // Trim slashes
                $key = trim($key, '/');
                $val = trim($val, '/');


                if (preg_match('#^' . $key . '#u', $uri)) {

                    if (strpos($val, '$') !== FALSE) {
                        // Use regex routing

                        $routedUri = preg_replace('#^' . $key . '$#u', $val, $uri);
                    } else {
                        // Standard routing
                        $routedUri = $val;
                    }

                    // A valid route has been found
                    break;
                }
            }
        }

        if (isset($routes[$routedUri])) {
            // Check for double routing (without regex)
            $routedUri = $routes[$routedUri];
        }

        return trim($routedUri, '/');
    }

    public static function currentUri() {
        return static::$current_uri;
    }

    public static function controllerDir() {
        return static::$controller_dir;
    }

    public static function controllerName() {
        return static::$controller;
    }

    public static function controllerUri() {
        return curl::base() . static::controllerDir() . static::controllerName();
    }

    public static function addRoute($route, $routedUri) {
        static::$routesRuntime[$route] = $routedUri;
    }

}

// End CFRouter