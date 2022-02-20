<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Routing\Route as SymfonyRoute;

class CFRouter {
    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $current_uri = '';

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $query_string = '';

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $complete_uri = '';

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $routed_uri = '';

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $url_suffix = '';

    /**
     * @var array
     *
     * @deprecated 1.3
     */
    public static $segments;

    /**
     * @var array
     *
     * @deprecated 1.3
     */
    public static $rsegments;

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $controller;

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $controller_dir;

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $controller_dir_ucfirst;

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $controller_path;

    /**
     * @var string
     *
     * @deprecated 1.3
     */
    public static $method = 'index';

    /**
     * @var array
     *
     * @deprecated 1.3
     */
    public static $arguments = [];

    /**
     * @var array
     *
     * @deprecated 1.3
     */
    public static $routeData = [];

    /**
     * CFRouter setup routine. Automatically called during CF setup process.
     *
     * @deprecated 1.3
     *
     * @return void
     */
    public static function setup() {
        self::resetup(self::$current_uri);

        if (self::$controller === null) {
            // No controller was found, so no page can be rendered
            if (defined('CFPUBLIC')) {
                if (carr::get(static::$segments, 0) == 'media'
                    || carr::get(static::$segments, 3) == 'media'
                    || (carr::get(static::$segments, 1) == 'cresenity' && carr::get(static::$segments, 2) == 'media')
                ) {
                    $response = CHTTP_FileServeDriver::responseStaticFile(static::$current_uri);

                    if ($response) {
                        self::$controller = $response;
                    }
                }
            }
        }
    }

    /**
     * CFRouter get route data.
     *
     * @param string $uri
     *
     * @deprecated 1.3
     *
     * @return CRouting_RouteData
     */
    public static function getRouteData($uri = null) {
        if (self::$routeData == null) {
            self::$routeData = [];
        }
        if (!isset(self::$routeData[$uri])) {
            self::$routeData[$uri] = static::routeDataToLegacyArray(new CRouting_RouteData($uri));
        }

        return self::$routeData[$uri];
    }

    protected static function routeDataToLegacyArray(CRouting_RouteData $routeData) {
        $data = [];
        $data['current_uri'] = $routeData->getUri();
        $data['query_string'] = $routeData->getQueryString();
        $data['complete_uri'] = $routeData->getCompleteUri();
        $data['routed_uri'] = $routeData->getRoutedUri();
        $data['url_suffix'] = $routeData->getUrlSuffix();
        $data['segments'] = $routeData->getSegments();
        $data['rsegments'] = $routeData->getRoutedSegments();
        $data['controller'] = $routeData->getController();
        $data['controller_dir'] = $routeData->getControllerDir();
        $data['controller_path'] = $routeData->getControllerPath();
        $data['method'] = $routeData->getMethod();
        $data['arguments'] = $routeData->getArguments();

        return $data;
    }

    /**
     * @param string $uri if null, using the current uri
     *
     * @deprecated 1.3
     *
     * @return void
     */
    public static function resetup($uri = null) {
    }

    public static function applyRouteData(CRouting_RouteData $routeData) {
        //self::$routes = carr::get($data, 'routes');
        self::$current_uri = $routeData->getUri();
        self::$query_string = $routeData->getQueryString();
        self::$complete_uri = $routeData->getCompleteUri();
        self::$routed_uri = $routeData->getRoutedUri();
        self::$url_suffix = $routeData->getUrlSuffix();
        self::$segments = $routeData->getSegments();
        self::$rsegments = $routeData->getRoutedSegments();
        self::$controller = $routeData->getController();
        self::$controller_dir = $routeData->getControllerDir();
        self::$controller_path = $routeData->getControllerPath();
        self::$method = $routeData->getMethod();
        self::$arguments = $routeData->getArguments();
    }

    /**
     * Attempts to determine the current URI using CLI, GET, PATH_INFO, ORIG_PATH_INFO, REQUEST_URI or PHP_SELF.
     *
     * @return string uri
     *
     * @deprecated 1.3
     */
    public static function getUri() {
        return self::$current_uri;
    }

    /**
     * Attempts to determine the current URI using CLI, GET, PATH_INFO, ORIG_PATH_INFO, or PHP_SELF.
     *
     * @deprecated 1.3
     *
     * @return string
     */
    public static function findUri() {
        return c::request()->path();
    }

    /**
     * @param string $uri
     * @param array  $routes
     *
     * @deprecated 1.3
     *
     * @return string
     */
    //@codingStandardsIgnoreStart
    public static function routed_uri($uri, &$routes = null) {
        return static::routedUri($uri, $routes);
    }

    //@codingStandardsIgnoreEnd

    /**
     * Generates routed URI from given URI.
     *
     * @param mixed  $uri
     * @param string $routes URI to convert
     *
     * @deprecated 1.3
     *
     * @return string Routed uri
     */
    public static function routedUri($uri, &$routes = null) {
        return self::$routed_uri;
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public static function currentUri() {
        return static::$current_uri;
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public static function controllerDir() {
        return static::$controller_dir;
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public static function controllerName() {
        return static::$controller;
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public static function controllerUri() {
        return curl::base() . static::controllerDir() . static::controllerName();
    }

    /**
     * @param string         $route
     * @param string|Closure $routedUri
     *
     * @deprecated 1.3
     *
     * @return void
     */
    public static function addRoute($route, $routedUri) {
        return CRouting_Manager::instance()->addUriRouting($route, $routedUri);
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public static function getController() {
        return static::$controller;
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public static function getControllerMethod() {
        return static::$method;
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public static function getCompleteUri() {
        return static::$complete_uri;
    }
}

// End CFRouter
