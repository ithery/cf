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
        $routeDataBefore = static::getRouteData($uri);
        $routedUri = CFRouter::routedUri($uri);
        $routeData = static::getRouteData($routedUri);
        if ($uri != $routedUri) {
            $routeData = $routeDataBefore;

            //cdbg::dd($uri, $routedUri, $routeDataBefore, $routeData);
            //$routedUri['seg']
        }
        CFRouter::applyRouteData($routeData);

        $controllerDir = carr::get($routeData, 'controller_dir_ucfirst', '');
        $controllerPrefix = str_replace('/', '_', $controllerDir);
        $controller = carr::get($routeData, 'controller', '');
        $className = 'Controller_' . $controllerPrefix . ucfirst($controller);

        $method = carr::get($routeData, 'method');

        $route = null;

        if (class_exists($className)) {
            $routedUri = strtolower($controllerDir) . $controller;

            $routedUri .= '/' . $method;

            $arguments = carr::get($routeData, 'arguments');
            //cdbg::dd($routeData);

            $parameters = [];
            foreach ($arguments as $key => $argument) {
                $routedUri .= "/{any$key}";
                $parameters[$key] = $argument;
            }

            //cdbg::dd($routedUri);
            //$routedUri = 't/ittron/feeds/hashtag/posts/{any0}';
            $route = new CRouting_Route(CRouting_Router::$verbs, $routedUri, $className . '@' . $method, $parameters);
        }
        return $route;
    }

    public static function getRouteData($uri) {
        $currentUri = null;
        $routes = null;
        if ($uri !== null) {
            $currentUri = $uri;
        }

        // Load routes
        $routesConfig = CFRouter::getRoutesConfig();
        $routesRuntime = CFRouter::getRoutesRuntime();
        $routes = array_merge($routesConfig, $routesRuntime);

        // Default route status
        $default_route = false;

        if ($currentUri === '') {
            // Make sure the default route is set
            if (!isset($routes['_default'])) {
                throw new CException('Please set a default route in config/routes.php');
            }
            // Use the default route when no segments exist
            $currentUri = $routes['_default'];

            // Default route is in use
            $default_route = true;
        }

        // Make sure the URL is not tainted with HTML characters
        $currentUri = chtml::specialchars($currentUri, false);

        // Remove all dot-paths from the URI, they are not valid
        $currentUri = preg_replace('#\.[\s./]*/#', '', $currentUri);

        $data = [];
        $data['routesConfig'] = $routesConfig;
        $data['routesRuntime'] = $routesRuntime;
        $data['routes'] = $routes;
        $data['current_uri'] = $currentUri;
        $data['query_string'] = '';
        $data['complete_uri'] = '';
        $data['routed_uri'] = '';
        $data['url_suffix'] = '';
        $data['segments'] = null;
        $data['rsegments'] = null;
        $data['controller'] = null;
        $data['controller_dir'] = null;
        $data['controller_dir_ucfirst'] = null;
        $data['controller_path'] = null;
        $data['method'] = 'index';
        $data['arguments'] = [];

        if (!empty($_SERVER['QUERY_STRING'])) {
            // Set the query string to the current query string
            $data['query_string'] = '?' . trim($_SERVER['QUERY_STRING'], '&/');
        }

        // At this point segments, rsegments, and current URI are all the same
        $data['segments'] = $data['rsegments'] = $data['current_uri'] = trim($data['current_uri'], '/');

        // Set the complete URI
        $data['complete_uri'] = $data['current_uri'] . $data['query_string'];

        // Explode the segments by slashes
        $data['segments'] = ($default_route === true or $data['segments'] === '') ? [] : explode('/', $data['segments']);

        if ($default_route === false and count($data['routes']) > 1) {
            // Custom routing

            $data['rsegments'] = CFRouter::routedUri($data['current_uri'], $data['routes']);
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
        $method_segment = null;

        // Paths to search
        $paths = CF::paths();

        foreach ($data['rsegments'] as $key => $segment) {
            // Add the segment to the search path
            $c_dir = $controller_path;
            $c_dir_ucfirst = ucfirst(strtolower($controller_path_ucfirst));
            $controller_path .= $segment;
            $controller_path_ucfirst .= ucfirst($segment);
            $found = false;

            foreach ($paths as $dir) {
                // Search within controllers only
                $dir .= 'controllers' . DS;

                if (is_dir($dir . $controller_path) or is_file($dir . $controller_path . EXT)) {
                    // Valid path
                    $found = true;

                    // The controller must be a file that exists with the search path
                    if ($c = str_replace('\\', '/', realpath($dir . $controller_path . EXT))
                        and is_file($c)
                    ) {
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

            if ($found === false) {
                // Maximum depth has been reached, stop searching
                break;
            }

            // Add another slash
            $controller_path .= '/';
            $controller_path_ucfirst .= '/';
        }

        if ($method_segment !== null and isset($data['rsegments'][$method_segment])) {
            // Set method
            $data['method'] = $data['rsegments'][$method_segment];

            if (isset($data['rsegments'][$method_segment + 1])) {
                // Set arguments
                $data['arguments'] = array_slice($data['rsegments'], $method_segment + 1);
            }
        }

        return $data;
    }
}
