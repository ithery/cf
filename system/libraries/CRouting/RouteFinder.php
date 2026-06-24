<?php

class CRouting_RouteFinder {
    /**
     * Return Route From Uri.
     *
     * @param string $uri
     *
     * @return CRouting_Route
     */
    public static function find($uri = null) {
        if ($uri == null) {
            $uri = PHP_SAPI == 'cli' ? CRouting_UrlFinder::getUri() : CHTTP::request()->path();

            $uri = trim($uri, '/');
        }

        $routeData = new CRouting_RouteData($uri);

        //$routeData = self::getRouteData($uri);

        CFRouter::applyRouteData($routeData);

        $controllerDir = carr::get($routeData, 'controller_dir_ucfirst', '');
        $className = $routeData->getControllerClass();

        $method = $routeData->getMethod();

        $route = null;

        if (class_exists($className) && static::isRoutableMethod($className, $method)) {
            $routedUri = strtolower($routeData->getControllerDir()) . $routeData->getController();
            $routedUri .= '/' . $method;

            $arguments = $routeData->getArguments();

            $parameters = [];
            foreach ($arguments as $key => $argument) {
                $routedUri .= "/{any" . $key . "}";
                $parameters[$key] = $argument;
            }

            $route = new CRouting_Route(CRouting_Router::$verbs, $routedUri, $className . '@' . $method, $parameters);

            $route->setRouteData($routeData);
        }

        return $route;
    }

    /**
     * @param string $className
     * @param string $method
     *
     * @return bool
     */
    protected static function isRoutableMethod($className, $method) {
        if (substr($method, 0, 2) === '__') {
            return false;
        }

        if (!method_exists($className, $method)) {
            return false;
        }

        $reflection = new ReflectionMethod($className, $method);

        if (!$reflection->isPublic()) {
            return false;
        }

        // Block methods inherited from CController that are not actions
        $declaringClass = $reflection->getDeclaringClass()->getName();
        if ($declaringClass === 'CController') {
            return false;
        }

        return true;
    }

    public static function controllerUrl($controller) {
        $classExplode = explode('_', $controller);
        $classExplode = array_map(function ($item) {
            return cstr::camel($item);
        }, $classExplode);
        $url = curl::base() . implode('/', array_slice($classExplode, 1)) . '/';

        return $url;
    }
}
