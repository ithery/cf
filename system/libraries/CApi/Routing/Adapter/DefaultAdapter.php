<?php

class CApi_Routing_Adapter_DefaultAdapter implements CApi_Contract_Routing_AdapterInterface {
    /**
     * Laravel router instance.
     *
     * @var \CRouting_Router
     */
    protected $router;

    /**
     * Array of registered routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Array of merged old routes and API routes.
     *
     * @var array
     */
    protected $mergedRoutes = [];

    /**
     * Routes already defined on the router.
     *
     * @var \CRouting_RouteCollection
     */
    protected $oldRoutes;

    /**
     * The globally available parameter patterns.
     *
     * @var array
     */
    protected $patterns = [];

    /**
     * Create a new laravel routing adapter instance.
     *
     * @param \CRouting_Router $router
     *
     * @return void
     */
    public function __construct(CRouting_Router $router) {
        $this->router = $router;
    }

    /**
     * Dispatch a request.
     *
     * @param \CHTTP_Request $request
     * @param string         $version
     *
     * @return mixed
     */
    public function dispatch(CHTTP_Request $request, $version) {
        if ($version == 'v1') {
            $this->routes['v1'] = new CRouting_RouteCollection();
        }
        if (!isset($this->routes[$version])) {
            throw new CApi_Exception_UnknownVersionException();
        }

        $routes = $this->mergeOldRoutes($version);

        $this->router->setRoutes($routes);

        $router = clone $this->router;

        $response = $router->dispatch($request);

        unset($router);

        return $response;
    }

    /**
     * Merge the old application routes with the API routes.
     *
     * @param string $version
     *
     * @return array|CRouting_RouteCollection
     */
    protected function mergeOldRoutes($version) {
        if (!isset($this->oldRoutes)) {
            $this->oldRoutes = $this->router->getRoutes();
        }

        if (!isset($this->mergedRoutes[$version])) {
            $this->mergedRoutes[$version] = $this->routes[$version];

            foreach ($this->oldRoutes as $route) {
                $this->mergedRoutes[$version]->add($route);
            }

            $this->mergedRoutes[$version]->refreshNameLookups();
            $this->mergedRoutes[$version]->refreshActionLookups();
        }

        return $this->mergedRoutes[$version];
    }

    /**
     * Get the URI, methods, and action from the route.
     *
     * @param mixed          $route
     * @param \CHTTP_Request $request
     *
     * @return array
     */
    public function getRouteProperties($route, CHTTP_Request $request) {
        if (method_exists($route, 'uri') && method_exists($route, 'methods')) {
            return [$route->uri(), $route->methods(), $route->getAction()];
        }

        return [$route->getUri(), $route->getMethods(), $route->getAction()];
    }

    /**
     * Add a route to the appropriate route collection.
     *
     * @param array  $methods
     * @param array  $versions
     * @param string $uri
     * @param mixed  $action
     *
     * @return \CRouting_Route
     */
    public function addRoute(array $methods, array $versions, $uri, $action) {
        $this->createRouteCollections($versions);

        // Add where-patterns from original laravel router
        $action['where'] = array_merge($this->router->getPatterns(), isset($action['where']) ? $action['where'] : []);

        $route = new CRouting_Route($methods, $uri, $action);
        $route->where($action['where']);

        foreach ($versions as $version) {
            $this->routes[$version]->add($route);
        }

        return $route;
    }

    /**
     * Create the route collections for the versions.
     *
     * @param array $versions
     *
     * @return void
     */
    protected function createRouteCollections(array $versions) {
        foreach ($versions as $version) {
            if (!isset($this->routes[$version])) {
                $this->routes[$version] = new CRouting_RouteCollection();
            }
        }
    }

    /**
     * Get all routes or only for a specific version.
     *
     * @param string $version
     *
     * @return mixed
     */
    public function getRoutes($version = null) {
        if (!is_null($version)) {
            return $this->routes[$version];
        }

        return $this->routes;
    }

    /**
     * Get a normalized iterable set of routes.
     *
     * @param string $version
     *
     * @return mixed
     */
    public function getIterableRoutes($version = null) {
        return $this->getRoutes($version);
    }

    /**
     * Set the routes on the adapter.
     *
     * @param array $routes
     *
     * @return void
     */
    public function setRoutes(array $routes) {
        $this->routes = $routes;
    }

    /**
     * Prepare a route for serialization.
     *
     * @param mixed $route
     *
     * @return mixed
     */
    public function prepareRouteForSerialization($route) {
        $route->prepareForSerialization();

        return $route;
    }

    /**
     * Gather the route middlewares.
     *
     * @param \CRouting_Route $route
     *
     * @return array
     */
    public function gatherRouteMiddlewares($route) {
        if (method_exists($this->router, 'gatherRouteMiddleware')) {
            return $this->router->gatherRouteMiddleware($route);
        }

        return $this->router->gatherRouteMiddlewares($route);
    }

    /**
     * Get the Laravel router instance.
     *
     * @return \Illuminate\Routing\Router
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * Get the global "where" patterns.
     *
     * @return array
     */
    public function getPatterns() {
        return $this->patterns;
    }
}
