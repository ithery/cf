<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * CF Controller class. The controller class must be extended to work
 * properly, so this class is defined as abstract.
 */
abstract class CController {
    /**
     * @var CController_Input
     *
     * @deprecated 1.4
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
        //boot trait
        $class = static::class;
        $booted = [];
        foreach (c::classUsesRecursive($class) as $trait) {
            $method = 'boot' . c::classBasename($trait);
            $classMethod = $class . $method;
            $reflectionClass = new ReflectionClass($class);

            if ($reflectionClass->hasMethod($method) && !in_array($classMethod, $booted)) {
                if ($reflectionClass->getMethod($method)->isStatic()) {
                    forward_static_call([$class, $method]);
                }
            }
        }
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
        if (!$this->isCallableAction($method)) {
            throw new CHTTP_Exception_NotFoundHttpException();
        }

        if (method_exists($this, $method)) {
            $reflectionMethod = new ReflectionMethod($this, $method);
            $requiredParameter = $reflectionMethod->getNumberOfRequiredParameters();

            if (count($parameters) < $requiredParameter) {
                throw new CHTTP_Exception_NotFoundHttpException();
            }
        }

        return $this->{$method}(...array_values($parameters));
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    protected function isCallableAction($method) {
        // Block all magic methods
        if (substr($method, 0, 2) === '__') {
            return false;
        }

        if (in_array($method, static::getBlockedMethods(), true)) {
            return false;
        }

        if (!method_exists($this, $method) && !method_exists($this, '__call')) {
            return false;
        }

        // Ensure method is public
        if (method_exists($this, $method)) {
            $reflection = new ReflectionMethod($this, $method);
            if (!$reflection->isPublic()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]
     */
    protected static function getBlockedMethods() {
        return [
            'callAction',
            'middleware',
            'getMiddleware',
        ];
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
