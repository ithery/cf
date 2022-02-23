<?php

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class CApi_Routing_Router {
    protected $apiGroup;

    /**
     * Routing adapter instance.
     *
     * @var \CApi_Contract_Routing_AdapterInterface
     */
    protected $adapter;

    /**
     * Accept parser instance.
     *
     * @var \Dingo\Api\Http\Parser\Accept
     */
    protected $accept;

    /**
     * Exception handler instance.
     *
     * @var \Dingo\Api\Contract\Debug\ExceptionHandler
     */
    protected $exception;

    /**
     * Group stack array.
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * Indicates if the request is conditional.
     *
     * @var bool
     */
    protected $conditionalRequest = true;

    /**
     * The current route being dispatched.
     *
     * @var \CApi_Routing_Route
     */
    protected $currentRoute;

    /**
     * The number of routes dispatched.
     *
     * @var int
     */
    protected $routesDispatched = 0;

    /**
     * The API domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * The API prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new router instance.
     *
     * @param \CApi_Contract_Routing_AdapterInterface $adapter
     * @param \CApi_Contract_ExceptionHandler         $exception
     * @param string                                  $domain
     * @param string                                  $prefix
     * @param mixed                                   $apiGroup
     *
     * @return void
     */
    public function __construct($apiGroup, CApi_Contract_Routing_AdapterInterface $adapter, CApi_Contract_ExceptionHandlerInterface $exception, $domain, $prefix) {
        $this->apiGroup = $apiGroup;
        $this->adapter = $adapter;
        $this->exception = $exception;
        $this->domain = $domain;
        $this->prefix = $prefix;
    }

    /**
     * @return CApi_Manager
     */
    public function manager() {
        return CApi_Manager::instance($this->apiGroup);
    }

    /**
     * An alias for calling the group method, allows a more fluent API
     * for registering a new API version group with optional
     * attributes and a required callback.
     *
     * This method can be called without the third parameter, however,
     * the callback should always be the last parameter.
     *
     * @param array|string   $version
     * @param array|callable $second
     * @param callable       $third
     *
     * @return void
     */
    public function version($version, $second, $third = null) {
        if (func_num_args() == 2) {
            list($version, $callback, $attributes) = array_merge(func_get_args(), [[]]);
        } else {
            list($version, $attributes, $callback) = func_get_args();
        }

        $attributes = array_merge($attributes, ['version' => $version]);

        $this->group($attributes, $callback);
    }

    /**
     * Create a new route group.
     *
     * @param array    $attributes
     * @param callable $callback
     *
     * @return void
     */
    public function group(array $attributes, $callback) {
        if (!isset($attributes['conditionalRequest'])) {
            $attributes['conditionalRequest'] = $this->conditionalRequest;
        }

        $attributes = $this->mergeLastGroupAttributes($attributes);

        if (!isset($attributes['version'])) {
            throw new RuntimeException('A version is required for an API group definition.');
        } else {
            $attributes['version'] = (array) $attributes['version'];
        }

        if ((!isset($attributes['prefix']) || empty($attributes['prefix'])) && isset($this->prefix)) {
            $attributes['prefix'] = $this->prefix;
        }

        if ((!isset($attributes['domain']) || empty($attributes['domain'])) && isset($this->domain)) {
            $attributes['domain'] = $this->domain;
        }

        $this->groupStack[] = $attributes;

        call_user_func($callback, $this);

        array_pop($this->groupStack);
    }

    /**
     * Create a new GET route.
     *
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function get($uri, $action) {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Create a new POST route.
     *
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function post($uri, $action) {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Create a new PUT route.
     *
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function put($uri, $action) {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Create a new PATCH route.
     *
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function patch($uri, $action) {
        return $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Create a new DELETE route.
     *
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function delete($uri, $action) {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Create a new OPTIONS route.
     *
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function options($uri, $action) {
        return $this->addRoute('OPTIONS', $uri, $action);
    }

    /**
     * Create a new route that responding to all verbs.
     *
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function any($uri, $action) {
        $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];

        return $this->addRoute($verbs, $uri, $action);
    }

    /**
     * Create a new route with the given verbs.
     *
     * @param array|string          $methods
     * @param string                $uri
     * @param array|string|callable $action
     *
     * @return mixed
     */
    public function match($methods, $uri, $action) {
        return $this->addRoute(array_map('strtoupper', (array) $methods), $uri, $action);
    }

    /**
     * Register an array of resources.
     *
     * @param array $resources
     *
     * @return void
     */
    public function resources(array $resources) {
        foreach ($resources as $name => $resource) {
            $options = [];

            if (is_array($resource)) {
                list($resource, $options) = $resource;
            }

            $this->resource($name, $resource, $options);
        }
    }

    /**
     * Register a resource controller.
     *
     * @param string $name
     * @param string $controller
     * @param array  $options
     *
     * @return void
     */
    public function resource($name, $controller, array $options = []) {
        if ($this->container->bound(ResourceRegistrar::class)) {
            $registrar = $this->container->make(ResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        $registrar->register($name, $controller, $options);
    }

    /**
     * Add a route to the routing adapter.
     *
     * @param string|array          $methods
     * @param string                $uri
     * @param string|array|callable $action
     *
     * @return mixed
     */
    public function addRoute($methods, $uri, $action) {
        if (is_string($action)) {
            $action = ['uses' => $action, 'controller' => $action];
        } elseif ($action instanceof Closure) {
            $action = [$action];
        } elseif (is_array($action)) {
            // For this sort of syntax $api->post('login', [LoginController::class, 'login']);
            if (is_string(carr::first($action)) && class_exists(carr::first($action)) && count($action) == 2) {
                $action = implode('@', $action);
                $action = ['uses' => $action, 'controller' => $action];
            }
        }

        $action = $this->mergeLastGroupAttributes($action);

        $action = $this->addControllerMiddlewareToRouteAction($action);

        $uri = $uri === '/' ? $uri : '/' . trim($uri, '/');

        if (!empty($action['prefix'])) {
            $uri = '/' . ltrim(rtrim(trim($action['prefix'], '/') . '/' . trim($uri, '/'), '/'), '/');

            unset($action['prefix']);
        }

        $action['uri'] = $uri;

        return $this->adapter->addRoute((array) $methods, $action['version'], $uri, $action);
    }

    /**
     * Add the controller preparation middleware to the beginning of the routes middleware.
     *
     * @param array $action
     *
     * @return array
     */
    protected function addControllerMiddlewareToRouteAction(array $action) {
        array_unshift($action['middleware'], 'api.controllers');

        return $action;
    }

    /**
     * Merge the last groups attributes.
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function mergeLastGroupAttributes(array $attributes) {
        if (empty($this->groupStack)) {
            return $this->mergeGroup($attributes, []);
        }

        return $this->mergeGroup($attributes, end($this->groupStack));
    }

    /**
     * Merge the given group attributes.
     *
     * @param array $new
     * @param array $old
     *
     * @return array
     */
    protected function mergeGroup(array $new, array $old) {
        $new['namespace'] = $this->formatNamespace($new, $old);

        $new['prefix'] = $this->formatPrefix($new, $old);

        foreach (['middleware', 'providers', 'scopes', 'before', 'after'] as $option) {
            $new[$option] = $this->formatArrayBasedOption($option, $new);
        }

        if (isset($new['domain'])) {
            unset($old['domain']);
        }

        if (isset($new['conditionalRequest'])) {
            unset($old['conditionalRequest']);
        }

        if (isset($new['uses'])) {
            $new['uses'] = $this->formatUses($new, $old);
        }

        $new['where'] = array_merge(carr::get($old, 'where', []), carr::get($new, 'where', []));

        if (isset($old['as'])) {
            $new['as'] = trim($old['as'] . '.' . carr::get($new, 'as', ''), '.');
        }

        return array_merge_recursive(carr::except($old, ['namespace', 'prefix', 'where', 'as']), $new);
    }

    /**
     * Format an array based option in a route action.
     *
     * @param string $option
     * @param array  $new
     *
     * @return array
     */
    protected function formatArrayBasedOption($option, array $new) {
        $value = carr::get($new, $option, []);

        return is_string($value) ? explode('|', $value) : $value;
    }

    /**
     * Format the uses key in a route action.
     *
     * @param array $new
     * @param array $old
     *
     * @return string
     */
    protected function formatUses(array $new, array $old) {
        if (isset($old['namespace']) && is_string($new['uses']) && strpos($new['uses'], '\\') !== 0) {
            return $old['namespace'] . '\\' . $new['uses'];
        }

        return $new['uses'];
    }

    /**
     * Format the namespace for the new group attributes.
     *
     * @param array $new
     * @param array $old
     *
     * @return string
     */
    protected function formatNamespace(array $new, array $old) {
        if (isset($new['namespace'], $old['namespace'])) {
            return trim($old['namespace'], '\\') . '\\' . trim($new['namespace'], '\\');
        } elseif (isset($new['namespace'])) {
            return trim($new['namespace'], '\\');
        }

        return carr::get($old, 'namespace');
    }

    /**
     * Format the prefix for the new group attributes.
     *
     * @param array $new
     * @param array $old
     *
     * @return string
     */
    protected function formatPrefix($new, $old) {
        if (isset($new['prefix'])) {
            return trim((string) carr::get($old, 'prefix'), '/') . '/' . trim((string) $new['prefix'], '/');
        }

        return carr::get($old, 'prefix', '');
    }

    /**
     * Dispatch the request to the application.
     *
     * @param CApi_HTTP_Request $request
     *
     * @return \CApi_HTTP_Response
     */
    public function dispatch(CApi_HTTP_Request $request) {
        $this->currentRequest = $request;

        $methodResolver = $this->manager()->getMethodResolver();

        $methodClass = call_user_func($methodResolver, [$request]);
        $method = CApi_Factory::createMethod($methodClass, $request);
        /**
         * @var CApi_MethodAbstract $method
         */
        $method->setApiRequest($request);
        CApi::runner()->runMethod($method);

        return $this->prepareResponse($method, $request, $request->format());

        return $this->dispatchToRoute($request);
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param CApi_HTTP_Request $request
     *
     * @return \CApi_HTTP_Response
     */
    public function dispatchToRoute(CApi_HTTP_Request $request) {
        return $this->runRoute($request, $this->findRoute($request));
    }

    /**
     * Find the route matching a given request.
     *
     * @param CApi_HTTP_Request $request
     *
     * @return CApi_Routing_Route
     */
    protected function findRoute($request) {
        $routeResolver = $this->manager()->getMethodResolver();

        $this->current = $route = $this->routes->match($request);

        return $route;
    }

    /**
     * Return the response for the given route.
     *
     * @param CApi_HTTP_Request  $request
     * @param CApi_Routing_Route $route
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function runRoute(CApi_HTTP_Request $request, CApi_Routing_Route $route) {
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });
    }

    /**
     * Gather the middleware for the given route with resolved class names.
     *
     * @param CApi_Routing_Route $route
     *
     * @return array
     */
    public function gatherRouteMiddleware(CApi_Routing_Route $route) {
        $excluded = c::collect($route->excludedMiddleware())->map(function ($name) {
            return (array) CMiddleware_MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
        })->flatten()->values()->all();

        $middleware = c::collect($route->gatherMiddleware())->map(function ($name) {
            return (array) CMiddleware_MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
        })->flatten()->reject(function ($name) use ($excluded) {
            if (empty($excluded)) {
                return false;
            } elseif (in_array($name, $excluded, true)) {
                return true;
            }

            if (!class_exists($name)) {
                return false;
            }

            $reflection = new ReflectionClass($name);

            return c::collect($excluded)->contains(function ($exclude) use ($reflection) {
                return class_exists($exclude) && $reflection->isSubclassOf($exclude);
            });
        })->values();

        return $this->sortMiddleware($middleware);
    }

    /**
     * Run the given route within a Stack "onion" instance.
     *
     * @param CApi_Routing_Route $route
     * @param CApi_HTTP_Request  $request
     *
     * @return mixed
     */
    protected function runRouteWithinStack(CApi_Routing_Route $route, CApi_HTTP_Request $request) {
        $shouldSkipMiddleware = CHTTP::shouldSkipMiddleware();

        $middleware = $shouldSkipMiddleware ? [] : $this->gatherRouteMiddleware($route);

        return (new CApi_HTTP_Pipeline())
            ->send($request)
            ->through($middleware)
            ->then(function ($request) use ($route) {
                cdbg::dd($route);
            });
    }

    /**
     * Sort the given middleware by priority.
     *
     * @param CCollection $middlewares
     *
     * @return array
     */
    protected function sortMiddleware(CCollection $middlewares) {
        return (new CMiddleware_SortedMiddleware($this->middlewarePriority, $middlewares))->all();
    }

    /**
     * @return CContainer_Container
     */
    protected function container() {
        return c::container();
    }

    /**
     * Prepare a response by transforming and formatting it correctly.
     *
     * @param mixed              $response
     * @param \CApi_HTTP_Request $request
     * @param string             $format
     *
     * @return \CApi_HTTP_Response
     */
    protected function prepareResponse($response, CApi_HTTP_Request $request, $format) {
        if ($response instanceof CHTTP_Response) {
            $response = CApi_HTTP_Response::makeFromExisting($response);
        } elseif ($response instanceof CHTTP_JsonResponse) {
            $response = CApi_HTTP_Response::makeFromJson($response);
        }

        if ($response instanceof CApi_HTTP_Response) {
            // If we try and get a formatter that does not exist we'll let the exception
            // handler deal with it. At worst we'll get a generic JSON response that
            // a consumer can hopefully deal with. Ideally they won't be using
            // an unsupported format.
            try {
                $response->getFormatter($format)->setResponse($response)->setRequest($request);
            } catch (NotAcceptableHttpException $exception) {
                return $this->exception->handle($exception);
            }

            $response = $response->morph($format);
        }

        if ($response->isSuccessful() && $this->requestIsConditional()) {
            if (!$response->headers->has('ETag')) {
                $response->setEtag(sha1($response->getContent() ?: ''));
            }

            $response->isNotModified($request);
        }

        return $response;
    }

    /**
     * Gather the middleware for the given route.
     *
     * @param mixed $route
     *
     * @return array
     */
    public function gatherRouteMiddlewares($route) {
        return $this->adapter->gatherRouteMiddlewares($route);
    }

    /**
     * Determine if the request is conditional.
     *
     * @return bool
     */
    protected function requestIsConditional() {
        return $this->getCurrentRoute()->requestIsConditional();
    }

    /**
     * Set the conditional request.
     *
     * @param bool $conditionalRequest
     *
     * @return void
     */
    public function setConditionalRequest($conditionalRequest) {
        $this->conditionalRequest = $conditionalRequest;
    }

    /**
     * Get the current request instance.
     *
     * @return \CApi_HTTP_Request
     */
    public function getCurrentRequest() {
        return $this->container['request'];
    }

    /**
     * Get the current route instance.
     *
     * @return \CApi_Routing_Route
     */
    public function getCurrentRoute() {
        if (isset($this->currentRoute)) {
            return $this->currentRoute;
        } elseif (!$this->hasDispatchedRoutes() || !$route = $this->container['request']->route()) {
            return;
        }

        return $this->currentRoute = $this->createRoute($route);
    }

    /**
     * Get the currently dispatched route instance.
     *
     * @return \CRouting_Route
     */
    public function current() {
        return $this->getCurrentRoute();
    }

    /**
     * Create a new route instance from an adapter route.
     *
     * @param array|\CRouting_Route $route
     *
     * @return \CApi_Routing_Route
     */
    public function createRoute($route) {
        return new CApi_Routing_Route($this->adapter, $this->container, $this->container['request'], $route);
    }

    /**
     * Set the current route instance.
     *
     * @param \CApi_Routing_Route $route
     *
     * @return void
     */
    public function setCurrentRoute(CApi_Routing_Route $route) {
        $this->currentRoute = $route;
    }

    /**
     * Determine if the router has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack() {
        return !empty($this->groupStack);
    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    public function getLastGroupPrefix() {
        if (empty($this->groupStack)) {
            return '';
        }

        $group = end($this->groupStack);

        return $group['prefix'];
    }

    /**
     * Get all routes registered on the adapter.
     *
     * @param string $version
     *
     * @return mixed
     */
    public function getRoutes($version = null) {
        $routes = $this->adapter->getIterableRoutes($version);

        if (!is_null($version)) {
            $routes = [$version => $routes];
        }

        $collections = [];

        foreach ($routes as $key => $value) {
            $collections[$key] = new CApi_Routing_RouteCollection(CApi::request());

            foreach ($value as $route) {
                $route = $this->createRoute($route);

                $collections[$key]->add($route);
            }
        }

        return is_null($version) ? $collections : $collections[$version];
    }

    /**
     * Get the raw adapter routes.
     *
     * @return array
     */
    public function getAdapterRoutes() {
        return $this->adapter->getRoutes();
    }

    /**
     * Set the raw adapter routes.
     *
     * @param array $routes
     *
     * @return void
     */
    public function setAdapterRoutes(array $routes) {
        $this->adapter->setRoutes($routes);

        $this->container->instance('api.routes', $this->getRoutes());
    }

    /**
     * Get the number of routes dispatched.
     *
     * @return int
     */
    public function getRoutesDispatched() {
        return $this->routesDispatched;
    }

    /**
     * Determine if the router has dispatched any routes.
     *
     * @return bool
     */
    public function hasDispatchedRoutes() {
        return $this->routesDispatched > 0;
    }

    /**
     * Get the current route name.
     *
     * @return null|string
     */
    public function currentRouteName() {
        return $this->current() ? $this->current()->getName() : null;
    }

    /**
     * Alias for the "currentRouteNamed" method.
     *
     * @param mixed string
     *
     * @return bool
     */
    public function is() {
        foreach (func_get_args() as $pattern) {
            if (cstr::is($pattern, $this->currentRouteName())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the current route matches a given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function currentRouteNamed($name) {
        return $this->current() ? $this->current()->getName() == $name : false;
    }

    /**
     * Get the current route action.
     *
     * @return null|string
     */
    public function currentRouteAction() {
        if (!$route = $this->current()) {
            return;
        }

        $action = $route->getAction();

        return is_string($action['uses']) ? $action['uses'] : null;
    }

    /**
     * Alias for the "currentRouteUses" method.
     *
     * @param  mixed  string
     *
     * @return bool
     */
    public function uses() {
        foreach (func_get_args() as $pattern) {
            if (cstr::is($pattern, $this->currentRouteAction())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the current route action matches a given action.
     *
     * @param string $action
     *
     * @return bool
     */
    public function currentRouteUses($action) {
        return $this->currentRouteAction() == $action;
    }

    /**
     * Flush the router's middleware groups.
     *
     * @return $this
     */
    public function flushMiddlewareGroups() {
        return $this;
    }
}
