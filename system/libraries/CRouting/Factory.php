<?php

class CRouting_Factory {
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function parseRoute($routeName) {
        // $routeName = CRouting_Helper::normalize($routeName);
        // list($controller, $method, $arguments) = $this->parseRouteName($routeName);
        // cdbg::dd($controller, $method, $arguments);
        // $route= new CRouting_Route()
        // $methods, $uri, $action, $parameters = null
        // find controller for this route
    }

    public function parseRouteName($routeName) {
        $segments = explode('.', $routeName);
        $paths = CF::paths(null, false, false);

        $controllerPath = '';
        $controllerFullPath = null;
        $controller = 'Controller';
        $method = 'index';
        $arguments = [];
        $methodSegmentKey = null;
        foreach ($segments as $key => $segment) {
            // Add the segment to the search path
            if (strlen($controllerPath) > 0) {
                $controllerPath .= DS;
            }
            $controllerPath .= $segment;
            $controller .= '_' . ucfirst($segment);
            $found = false;

            foreach ($paths as $dir) {
                // Search within controllers only
                $dir .= 'controllers' . DS;
                if (!is_dir($dir . $controllerPath) && !is_file($dir . $controllerPath . EXT)) {
                    break;
                }

                $found = true;
                // The controller must be a file that exists with the search path
                if ($c = str_replace('\\', '/', realpath($dir . $controllerPath . EXT))
                    and is_file($c)
                ) {
                    $controllerFullPath = $c;
                    $methodSegmentKey = $key + 1;

                    break;
                }
            }

            if ($found === false) {
                // Maximum depth has been reached, stop searching
                break;
            }
        }
        if ($methodSegmentKey !== null and isset($segments[$methodSegmentKey])) {
            // Set method
            $method = $segments[$methodSegmentKey];

            if (isset($segments[$methodSegmentKey + 1])) {
                // Set arguments
                $arguments = array_slice($segments, $methodSegmentKey + 1);
            }
        }

        return [$controller, $method, $arguments];
    }
}
