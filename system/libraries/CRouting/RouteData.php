<?php

class CRouting_RouteData {
    protected $uri;

    protected $queryString;

    protected $completeUri;

    protected $urlSuffix;

    protected $segments;

    protected $routedSegments;

    protected $routedUri;

    protected $controllerPath;

    protected $controller;

    protected $controllerDirUcFirst;

    protected $controllerDir;

    protected $controllerClass;

    protected $method;

    protected $arguments;

    public function __construct($uri) {
        $this->uri = $uri;
        if ($this->uri == '') {
            $this->uri = CRouting_Manager::instance()->getDefaultRoute();
        }

        $this->uri = trim($this->uri, '/');

        if ($suffix = CF::config('core.url_suffix') and strpos($this->uri, $suffix) !== false) {
            // Remove the URL suffix
            $this->uri = preg_replace('#' . preg_quote($suffix) . '$#u', '', $this->uri);
            $this->urlSuffix = $suffix;
        }

        if (!empty($_SERVER['QUERY_STRING'])) {
            // Set the query string to the current query string
            $this->queryString = '?' . trim($_SERVER['QUERY_STRING'], '&/');
        }

        $this->completeUri = $this->uri . $this->queryString;
        $this->segments = explode('/', $this->uri);

        $this->routedUri = $this->getRoutedUriFromUriRouting($this->uri);
        $this->routedSegments = explode('/', $this->routedUri);

        // Prepare to find the controller
        $controllerPath = '';
        $controllerPathUcFirst = '';
        $controllerClass = '';
        $searchdir = '';
        $searcDirUcfirst = '';

        $methodSegment = null;

        // Paths to search
        $paths = CF::paths(null, false, false);

        foreach ($this->routedSegments as $key => $segment) {
            // Add the segment to the search path
            $searchdir = $controllerPath;
            $searcDirUcfirst = ucfirst($controllerPathUcFirst);
            $controllerPath .= $segment;
            $controllerPathUcFirst .= ucfirst($segment);
            $controllerClass .= ucfirst($segment);
            $found = false;

            foreach ($paths as $dir) {
                // Search within controllers only
                $dir .= 'controllers' . DS;

                if (is_dir($dir . $controllerPath) or is_file($dir . $controllerPath . EXT)) {
                    // Valid path
                    $found = true;

                    // The controller must be a file that exists with the search path
                    if ($c = str_replace('\\', '/', realpath($dir . $controllerPath . EXT))
                        and is_file($c)
                    ) {
                        // Set controller name
                        $this->controller = $segment;

                        // Set controller dir
                        $this->controllerDir = $searchdir;
                        $this->controllerDirUcFirst = $searcDirUcfirst;
                        $this->controllerClass = 'Controller_' . $controllerClass;
                        // Change controller path
                        $this->controllerPath = $c;

                        // Set the method segment
                        $methodSegment = $key + 1;

                        // Stop searching
                        break;
                    }
                }
            }

            if ($found === false) {
                // Maximum depth has been reached, stop searching
                break;
            }

            // Add another slash
            $controllerPath .= '/';
            $controllerPathUcFirst .= '/';
            $controllerClass .= '_';
        }
        $this->method = 'index';
        if ($methodSegment !== null and isset($this->routedSegments[$methodSegment])) {
            // Set method
            $this->method = $this->routedSegments[$methodSegment];

            if (count($this->routedSegments) > $methodSegment) {
                $this->arguments = array_slice($this->routedSegments, $methodSegment + 1);
            }
        }
    }

    public function getRoutedUriFromUriRouting($uri) {
        $routes = CRouting_Manager::instance()->getUriRoutings();
        // Prepare variables
        $routedUri = $uri;

        if (isset($routes[$uri])) {
            // Literal match, no need for regex
            $routedUri = $routes[$uri];
        } else {
            // Loop through the routes and see if anything matches
            foreach ($routes as $key => $val) {
                if ($key === '_default') {
                    continue;
                }
                if (is_callable($val)) {
                    preg_match_all("/{([\w]*)}/", $key, $matches, PREG_SET_ORDER);
                    $callbackArgs = [$uri];
                    $bracketKeys = [];
                    foreach ($matches as $matchedVal) {
                        $str = $matchedVal[1]; //matches str without bracket {}
                        $bStr = $matchedVal[0]; //matches str with bracket {}
                        $bracketKeys[] = null;
                        $key = str_replace($bStr, '(.+?)', $key);
                    }

                    $matchesBracket = false;
                    $key = str_replace('/', "\/", $key);
                    preg_match('#' . $key . '#ims', $uri, $matches);

                    if (preg_match('#' . $key . '#ims', $uri, $matches)) {
                        $matchesBracket = array_slice($matches, 1);
                        $matchesBracket ? $callbackArgs = array_merge($callbackArgs, $matchesBracket) : $callbackArgs = array_merge($callbackArgs, $bracketKeys);
                        $val = call_user_func_array($val, $callbackArgs);
                    } else {
                        $val = null;
                    }

                    if ($val == null) {
                        continue;
                    }
                }

                // Trim slashes
                $key = trim($key, '/');
                $val = trim($val, '/');
                if (preg_match('#^' . $key . '#ims', $uri)) {
                    if (strpos($val, '$') !== false) {
                        // Use regex routing

                        $routedUri = preg_replace('#^' . $key . '$#ims', $val, $uri);
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

    public function getUri() {
        return $this->uri;
    }

    public function getQueryString() {
        return $this->queryString;
    }

    public function getRoutedUri() {
        return $this->routedUri;
    }

    public function getRoutedSegments() {
        return $this->routedSegments;
    }

    public function getSegments() {
        return $this->segments;
    }

    public function getCompleteUri() {
        return $this->completeUri;
    }

    public function getUrlSuffix() {
        return $this->urlSuffix;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function getController() {
        return $this->controller;
    }

    public function getControllerDir() {
        return $this->controllerDir;
    }

    public function getControllerPath() {
        return $this->controllerPath;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getArguments() {
        return $this->arguments ?: [];
    }

    public function toArray() {
        $data = [];
        $data['uri'] = $this->getUri();
        $data['queryString'] = $this->getQueryString();
        $data['completeUri'] = $this->getCompleteUri();
        $data['routedUri'] = $this->getRoutedUri();
        $data['urlSuffix'] = $this->getUrlSuffix();
        $data['segments'] = $this->getSegments();
        $data['routedSegments'] = $this->getRoutedSegments();
        $data['controller'] = $this->getController();
        $data['controllerDir'] = $this->getControllerDir();
        $data['controllerPath'] = $this->getControllerPath();
        $data['method'] = $this->getMethod();
        $data['arguments'] = $this->getArguments();

        return $data;
    }
}
