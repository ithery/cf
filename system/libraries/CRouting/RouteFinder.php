<?php

/**
 * Description of RouteFinder.
 *
 * @author Hery
 */
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
            $uri = CHTTP::request()->path();
            if (PHP_SAPI == 'cli') {
                $uri = CRouting_UrlFinder::getUri();
            }

            $uri = trim($uri, '/');
        }

        $routeData = new CRouting_RouteData($uri);

        //$routeData = self::getRouteData($uri);

        CFRouter::applyRouteData($routeData);

        $controllerDir = carr::get($routeData, 'controller_dir_ucfirst', '');
        $className = $routeData->getControllerClass();

        $method = $routeData->getMethod();

        $route = null;

        if (class_exists($className)) {
            $routedUri = strtolower($routeData->getControllerDir()) . $routeData->getController();
            $routedUri .= '/' . $method;

            $arguments = $routeData->getArguments();
            //cdbg::dd($routeData);

            $parameters = [];
            foreach ($arguments as $key => $argument) {
                $routedUri .= "/{any${key}}";
                $parameters[$key] = $argument;
            }

            //cdbg::dd($routedUri);
            //$routedUri = 't/ittron/feeds/hashtag/posts/{any0}';
            $route = new CRouting_Route(CRouting_Router::$verbs, $routedUri, $className . '@' . $method, $parameters);
        }

        return $route;
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
