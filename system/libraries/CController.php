<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * CF Controller class. The controller class must be extended to work
 * properly, so this class is defined as abstract.
 */
abstract class CController {
    /**
     * @var string
     *
     * @deprecated 1.3
     */
    protected $baseUri;

    /**
     * @var CController_Input
     *
     * @deprecated 1.3
     */
    protected $input;

    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Loads URI, and Input into this controller.
     *
     * @return void
     */
    public function __construct() {

        // Input should always be available
        $this->input = CController_Input::instance();

        $this->baseUri = CFRouter::controllerUri();
    }

    /**
     * Register middleware on the controller.
     *
     * @param \Closure|array|string $middleware
     * @param array                 $options
     *
     * @return \CController_MiddlewareOptions
     */
    public function middleware($middleware, array $options = []) {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new CController_MiddlewareOptions($options);
    }

    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware() {
        return $this->middleware;
    }

    /**
     * Execute an action on the controller.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters) {
        if (!method_exists($this, $method) && !method_exists($this, '__call')) {
            throw new CHTTP_Exception_NotFoundHttpException();
        }
        if (method_exists($this, $method)) {
            $reflectionClass = new ReflectionClass($this);

            $reflectionMethod = $reflectionClass->getMethod($method);
            /** @var ReflectionMethod $reflectionMethod */
            $requiredParameter = $reflectionMethod->getNumberOfRequiredParameters();

            if (count($parameters) < $requiredParameter) {
                throw new CHTTP_Exception_NotFoundHttpException();
            }
        }

        return $this->{$method}(...array_values($parameters));
    }

    public static function controllerUrl() {
        $class = get_called_class();
        $classExplode = explode('_', $class);
        $classExplode = array_map(function ($item) {
            return cstr::camel($item);
        }, $classExplode);
        $url = curl::base() . implode('/', array_slice($classExplode, 1)) . '/';

        return $url;
    }
}

// End Controller Class
