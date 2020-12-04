<?php

/**
 * Description of RouteFinder
 *
 * @author Hery
 */
class CRouting_RouteFinder {

    public static function find($uri = null) {
        if ($uri == null) {
            $uri = CHTTP::request()->path();
            $uri = trim($uri, '/');
        }
        $routeData = static::getRouteData($uri);
        $controllerDir = str_replace('/', '_', carr::get($routeData, 'controller_dir_ucfirst', ''));
        $controller = carr::get($routeData, 'controller', '');
        $className = 'Controller_' . $controllerDir . ucfirst($controller);

        
        $method = carr::get($routeData, 'method');
        $route=null;
        if (class_exists($className)) {
            
            $route = new CRouting_Route(CRouting_Router::$verbs, $uri, $className . '@' . $method);

            $arguments = carr::get($routeData, 'arguments');
            foreach ($arguments as $key => $argument) {
                $route->setParameter($key, $argument);
            }
        }
        return $route;
    }

    public static function getRouteData($uri) {

        $currentUri = NULL;
        $routes = NULL;
        if ($uri !== null) {
            $currentUri = $uri;
        }

        if ($currentUri === null) {
            $currentUri = self::getUri();
        }

        // Load routes
        $routesConfig = CFRouter::getRoutesConfig();
        $routesRuntime = CFRouter::getRoutesRuntime();
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


        return $data;
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
                    $bracketKeys = [];
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
}
