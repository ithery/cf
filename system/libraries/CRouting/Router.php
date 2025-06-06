<?php

/**
 * Description of Router.
 *
 * @author Hery
 */

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/* implements  BindingRegistrar, RegistrarContract */
/**
 * @mixin CRouting_RouteRegistrar
 */
class CRouting_Router {
    use CTrait_Macroable {
        __call as macroCall;
    }

    /**
     * The priority-sorted list of middleware.
     *
     * Forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    public $middlewarePriority = [];

    /**
     * All of the verbs supported by the router.
     *
     * @var string[]
     */
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * The event dispatcher instance.
     *
     * @var CEvent_Dispatcher
     */
    protected $events;

    /**
     * The IoC container instance.
     *
     * @var CContainer_Container
     */
    protected $container;

    /**
     * The route collection instance.
     *
     * @var CRouting_RouteCollectionInterface
     */
    protected $routes;

    /**
     * The currently dispatched route instance.
     *
     * @var null|CRouting_Route
     */
    protected $current;

    /**
     * The request currently being dispatched.
     *
     * @var CHTTP_Request
     */
    protected $currentRequest;

    /**
     * All of the short-hand keys for middlewares.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * All of the middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The registered route value binders.
     *
     * @var array
     */
    protected $binders = [];

    /**
     * The globally available parameter patterns.
     *
     * @var array
     */
    protected $patterns = [];

    /**
     * The route group attribute stack.
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * @var CRouting_Router
     */
    private static $instance;

    /**
     * @return CRouting_Router
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CRouting_Router();
        }

        return static::$instance;
    }

    /**
     * Create a new Router instance.
     *
     * @return void
     */
    public function __construct() {
        $this->events = CEvent::dispatcher();
        $this->routes = new CRouting_RouteCollection();
        $this->container = CContainer::getInstance();
        $this->middleware = CMiddleware::middleware();
    }

    /**
     * Register a new GET route with the router.
     *
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function get($uri, $action = null) {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function post($uri, $action = null) {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register a new PUT route with the router.
     *
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function put($uri, $action = null) {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register a new PATCH route with the router.
     *
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function patch($uri, $action = null) {
        return $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Register a new DELETE route with the router.
     *
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function delete($uri, $action = null) {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Register a new OPTIONS route with the router.
     *
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function options($uri, $action = null) {
        return $this->addRoute('OPTIONS', $uri, $action);
    }

    /**
     * Register a new route responding to all verbs.
     *
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function any($uri, $action = null) {
        return $this->addRoute(self::$verbs, $uri, $action);
    }

    /**
     * Register a new Fallback route with the router.
     *
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function fallback($action) {
        $placeholder = 'fallbackPlaceholder';

        return $this->addRoute(
            'GET',
            "{{$placeholder}}",
            $action
        )->where($placeholder, '.*')->fallback();
    }

    /**
     * Create a redirect from one URI to another.
     *
     * @param string $uri
     * @param string $destination
     * @param int    $status
     *
     * @return CRouting_Route
     */
    public function redirect($uri, $destination, $status = 302) {
        return $this->any($uri, 'CController_RedirectController')
            ->defaults('destination', $destination)
            ->defaults('status', $status);
    }

    /**
     * Create a permanent redirect from one URI to another.
     *
     * @param string $uri
     * @param string $destination
     *
     * @return CRouting_Route
     */
    public function permanentRedirect($uri, $destination) {
        return $this->redirect($uri, $destination, 301);
    }

    /**
     * Register a new route that returns a view.
     *
     * @param string    $uri
     * @param string    $view
     * @param array     $data
     * @param int|array $status
     * @param array     $headers
     *
     * @return CRouting_Route
     */
    public function view($uri, $view, $data = [], $status = 200, array $headers = []) {
        return $this->match(['GET', 'HEAD'], $uri, '\Illuminate\Routing\ViewController')
            ->setDefaults([
                'view' => $view,
                'data' => $data,
                'status' => is_array($status) ? 200 : $status,
                'headers' => is_array($status) ? $status : $headers,
            ]);
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param array|string               $methods
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function match($methods, $uri, $action = null) {
        return $this->addRoute(array_map('strtoupper', (array) $methods), $uri, $action);
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param array           $attributes
     * @param \Closure|string $routes
     *
     * @return void
     */
    public function group(array $attributes, $routes) {
        $this->updateGroupStack($attributes);

        // Once we have updated the group stack, we'll load the provided routes and
        // merge in the group's attributes when the routes are created. After we
        // have created the routes, we will pop the attributes off the stack.
        $this->loadRoutes($routes);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param array $attributes
     *
     * @return void
     */
    protected function updateGroupStack(array $attributes) {
        if ($this->hasGroupStack()) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     *
     * @param array $new
     * @param bool  $prependExistingPrefix
     *
     * @return array
     */
    public function mergeWithLastGroup($new, $prependExistingPrefix = true) {
        return CRouting_RouteGroup::merge($new, end($this->groupStack), $prependExistingPrefix);
    }

    /**
     * Load the provided routes.
     *
     * @param \Closure|string $routes
     *
     * @return void
     */
    protected function loadRoutes($routes) {
        if ($routes instanceof Closure) {
            $routes($this);
        } else {
            (new CRouting_RouteFileRegistrar($this))->register($routes);
        }
    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    public function getLastGroupPrefix() {
        if ($this->hasGroupStack()) {
            $last = end($this->groupStack);

            return carr::get($last, 'prefix', '');
        }

        return '';
    }

    /**
     * Add a route to the underlying route collection.
     *
     * @param array|string               $methods
     * @param string                     $uri
     * @param null|array|string|callable $action
     *
     * @return CRouting_Route
     */
    public function addRoute($methods, $uri, $action) {
        return $this->routes->add($this->createRoute($methods, $uri, $action));
    }

    /**
     * Create a new route instance.
     *
     * @param array|string $methods
     * @param string       $uri
     * @param mixed        $action
     *
     * @return CRouting_Route
     */
    protected function createRoute($methods, $uri, $action) {
        // If the route is routing to a controller we will parse the route action into
        // an acceptable array format before registering it and creating this route
        // instance itself. We need to build the Closure that will call this out.
        if ($this->actionReferencesController($action)) {
            $action = $this->convertToControllerAction($action);
        }

        $route = $this->newRoute(
            $methods,
            $this->prefix($uri),
            $action
        );

        // If we have groups that need to be merged, we will merge them now after this
        // route has already been created and is ready to go. After we're done with
        // the merge we will be ready to return the route back out to the caller.
        if ($this->hasGroupStack()) {
            $this->mergeGroupAttributesIntoRoute($route);
        }

        $this->addWhereClausesToRoute($route);

        return $route;
    }

    /**
     * Determine if the action is routing to a controller.
     *
     * @param mixed $action
     *
     * @return bool
     */
    protected function actionReferencesController($action) {
        if (!$action instanceof Closure) {
            return is_string($action) || (isset($action['uses']) && is_string($action['uses']));
        }

        return false;
    }

    /**
     * Add a controller based route action to the action array.
     *
     * @param array|string $action
     *
     * @return array
     */
    protected function convertToControllerAction($action) {
        if (is_string($action)) {
            $action = ['uses' => $action];
        }

        // Here we'll merge any group "uses" statement if necessary so that the action
        // has the proper clause for this property. Then we can simply set the name
        // of the controller on the action and return the action array for usage.
        if ($this->hasGroupStack()) {
            $action['uses'] = $this->prependGroupNamespace($action['uses']);
        }

        // Here we will set this controller name on the action array just so we always
        // have a copy of it for reference if we need it. This can be used while we
        // search for a controller name or do some other type of fetch operation.
        $action['controller'] = $action['uses'];

        return $action;
    }

    /**
     * Prepend the last group namespace onto the use clause.
     *
     * @param string $class
     *
     * @return string
     */
    protected function prependGroupNamespace($class) {
        $group = end($this->groupStack);

        return isset($group['namespace']) && strpos($class, '\\') !== 0 ? $group['namespace'] . '\\' . $class : $class;
    }

    /**
     * Create a new Route object.
     *
     * @param array|string $methods
     * @param string       $uri
     * @param mixed        $action
     *
     * @return CRouting_Route
     */
    public function newRoute($methods, $uri, $action) {
        return (new CRouting_Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    /**
     * Prefix the given URI with the last prefix.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function prefix($uri) {
        return trim(trim($this->getLastGroupPrefix(), '/') . '/' . trim($uri, '/'), '/') ?: '/';
    }

    /**
     * Add the necessary where clauses to the route based on its initial registration.
     *
     * @param CRouting_Route $route
     *
     * @return CRouting_Route
     */
    protected function addWhereClausesToRoute($route) {
        $route->where(array_merge(
            $this->patterns,
            carr::get($route->getAction(), 'where', [])
        ));

        return $route;
    }

    /**
     * Merge the group stack with the controller action.
     *
     * @param CRouting_Route $route
     *
     * @return void
     */
    protected function mergeGroupAttributesIntoRoute($route) {
        $route->setAction($this->mergeWithLastGroup(
            $route->getAction(),
            $prependExistingPrefix = false
        ));
    }

    /**
     * Return the response returned by the given route.
     *
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respondWithRoute($name) {
        $route = c::tap($this->routes->getByName($name))->bind($this->currentRequest);

        return $this->runRoute($this->currentRequest, $route);
    }

    /**
     * Dispatch the request to the application.
     *
     * @param CHTTP_Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatch(CHTTP_Request $request) {
        $this->currentRequest = $request;

        return $this->dispatchToRoute($request);
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param CHTTP_Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchToRoute(CHTTP_Request $request) {
        return $this->runRoute($request, $this->findRoute($request));
    }

    /**
     * Find the route matching a given request.
     *
     * @param CHttp_Request $request
     *
     * @return CRouting_Route
     */
    protected function findRoute($request) {
        $this->current = $route = $this->routes->match($request);

        //$this->container->instance(CHTTP_Route::class, $route);

        return $route;
    }

    /**
     * Return the response for the given route.
     *
     * @param CHTTP_Request  $request
     * @param CRouting_Route $route
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function runRoute(CHTTP_Request $request, CRouting_Route $route) {
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $this->events->dispatch(new CRouting_Event_RouteMatched($route, $request));

        return $this->prepareResponse(
            $request,
            $this->runRouteWithinStack($route, $request)
        );
    }

    /**
     * Run the given route within a Stack "onion" instance.
     *
     * @param CRouting_Route $route
     * @param CHTTP_Request  $request
     *
     * @return mixed
     */
    protected function runRouteWithinStack(CRouting_Route $route, CHTTP_Request $request) {
        $shouldSkipMiddleware = CHTTP::shouldSkipMiddleware();

        $middleware = $shouldSkipMiddleware ? [] : $this->gatherRouteMiddleware($route);

        return (new CHTTP_Pipeline())
            ->send($request)
            ->through($middleware)
            ->then(function ($request) use ($route) {
                return $this->prepareResponse(
                    $request,
                    $route->runWithOutputBuffer()
                );
            });
    }

    /**
     * Gather the middleware for the given route with resolved class names.
     *
     * @param CRouting_Route $route
     *
     * @return array
     */
    public function gatherRouteMiddleware(CRouting_Route $route) {
        return $this->resolveMiddleware($route->gatherMiddleware(), $route->excludedMiddleware());
    }

    /**
     * Resolve a flat array of middleware classes from the provided array.
     *
     * @param array $middleware
     * @param array $excluded
     *
     * @return array
     */
    public function resolveMiddleware(array $middleware, array $excluded = []) {
        $excluded = c::collect($excluded)->map(function ($name) {
            return (array) CRouting_MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
        })->flatten()->values()->all();

        $middleware = c::collect($middleware)->map(function ($name) {
            return (array) CRouting_MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
        })->flatten()->reject(function ($name) use ($excluded) {
            if (empty($excluded)) {
                return false;
            }

            if ($name instanceof Closure) {
                return false;
            }

            if (in_array($name, $excluded, true)) {
                return true;
            }

            if (!class_exists($name)) {
                return false;
            }

            $reflection = new ReflectionClass($name);

            return c::collect($excluded)->contains(
                function ($exclude) use ($reflection) {
                    return class_exists($exclude) && $reflection->isSubclassOf($exclude);
                }
            );
        })->values();

        return $this->sortMiddleware($middleware);
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
     * Create a response instance from the given value.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed                                     $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function prepareResponse($request, $response) {
        $this->events->dispatch(new CRouting_Event_PreparingResponse($request, $response));

        return c::tap(static::toResponse($request, $response), function ($response) use ($request) {
            $this->events->dispatch(new CRouting_Event_ResponsePrepared($request, $response));
        });
    }

    /**
     * Static version of prepareResponse.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed                                     $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function toResponse($request, $response) {
        if ($response instanceof CInterface_Responsable) {
            $response = $response->toResponse($request);
        }

        if ($response instanceof PsrResponseInterface) {
            $response = (new HttpFoundationFactory())->createResponse($response);
        } elseif ($response instanceof CModel && $response->wasRecentlyCreated) {
            $response = new CHTTP_JsonResponse($response, 201);
        } elseif (!$response instanceof SymfonyResponse
            && ($response instanceof Arrayable
            || $response instanceof Jsonable
            || $response instanceof ArrayObject
            || $response instanceof JsonSerializable
            || is_array($response))
        ) {
            $response = new CHTTP_JsonResponse($response);
        } elseif (!$response instanceof SymfonyResponse) {
            $response = new CHTTP_Response($response, 200, ['Content-Type' => 'text/html']);
        }

        if ($response->getStatusCode() === CHTTP_Response::HTTP_NOT_MODIFIED) {
            $response->setNotModified();
        }

        return $response->prepare($request);
    }

    /**
     * Substitute the route bindings onto the route.
     *
     * @param CRouting_Route $route
     *
     * @throws CModel_Exception_ModelNotFoundException
     *
     * @return CRouting_Route
     */
    public function substituteBindings($route) {
        foreach ($route->parameters() as $key => $value) {
            if (isset($this->binders[$key])) {
                $route->setParameter($key, $this->performBinding($key, $value, $route));
            }
        }

        return $route;
    }

    /**
     * Substitute the implicit Eloquent model bindings for the route.
     *
     * @param CRouting_Route $route
     *
     * @throws CModel_Exception_ModelNotFoundException
     *
     * @return void
     */
    public function substituteImplicitBindings($route) {
        CRouting_ImplicitRouteBinding::resolveForRoute($this->container, $route);
    }

    /**
     * Call the binding callback for the given key.
     *
     * @param string         $key
     * @param string         $value
     * @param CRouting_Route $route
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return mixed
     */
    protected function performBinding($key, $value, $route) {
        return call_user_func($this->binders[$key], $value, $route);
    }

    /**
     * Register a route matched event listener.
     *
     * @param string|callable $callback
     *
     * @return void
     */
    public function matched($callback) {
        $this->events->listen(CRouting_Event_RouteMatched::class, $callback);
    }

    /**
     * Get all of the defined middleware short-hand names.
     *
     * @return array
     */
    public function getMiddleware() {
        return $this->middleware;
    }

    /**
     * Register a short-hand name for a middleware.
     *
     * @param string $name
     * @param string $class
     *
     * @return $this
     */
    public function aliasMiddleware($name, $class) {
        $this->middleware[$name] = $class;

        return $this;
    }

    /**
     * Check if a middlewareGroup with the given name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasMiddlewareGroup($name) {
        return array_key_exists($name, $this->middlewareGroups);
    }

    /**
     * Get all of the defined middleware groups.
     *
     * @return array
     */
    public function getMiddlewareGroups() {
        return $this->middlewareGroups;
    }

    /**
     * Register a group of middleware.
     *
     * @param string $name
     * @param array  $middleware
     *
     * @return $this
     */
    public function middlewareGroup($name, array $middleware) {
        $this->middlewareGroups[$name] = $middleware;

        return $this;
    }

    /**
     * Add a middleware to the beginning of a middleware group.
     *
     * If the middleware is already in the group, it will not be added again.
     *
     * @param string $group
     * @param string $middleware
     *
     * @return $this
     */
    public function prependMiddlewareToGroup($group, $middleware) {
        if (isset($this->middlewareGroups[$group]) && !in_array($middleware, $this->middlewareGroups[$group])) {
            array_unshift($this->middlewareGroups[$group], $middleware);
        }

        return $this;
    }

    /**
     * Add a middleware to the end of a middleware group.
     *
     * If the middleware is already in the group, it will not be added again.
     *
     * @param string $group
     * @param string $middleware
     *
     * @return $this
     */
    public function pushMiddlewareToGroup($group, $middleware) {
        if (!array_key_exists($group, $this->middlewareGroups)) {
            $this->middlewareGroups[$group] = [];
        }

        if (!in_array($middleware, $this->middlewareGroups[$group])) {
            $this->middlewareGroups[$group][] = $middleware;
        }

        return $this;
    }

    /**
     * Add a new route parameter binder.
     *
     * @param string          $key
     * @param string|callable $binder
     *
     * @return void
     */
    public function bind($key, $binder) {
        $this->binders[str_replace('-', '_', $key)] = CRouting_RouteBinding::forCallback(
            $this->container,
            $binder
        );
    }

    /**
     * Register a model binder for a wildcard.
     *
     * @param string        $key
     * @param string        $class
     * @param null|\Closure $callback
     *
     * @return void
     */
    public function model($key, $class, Closure $callback = null) {
        $this->bind($key, CRouting_RouteBinding::forModel($this->container, $class, $callback));
    }

    /**
     * Get the binding callback for a given binding.
     *
     * @param string $key
     *
     * @return null|\Closure
     */
    public function getBindingCallback($key) {
        if (isset($this->binders[$key = str_replace('-', '_', $key)])) {
            return $this->binders[$key];
        }
    }

    /**
     * Get the global "where" patterns.
     *
     * @return array
     */
    public function getPatterns() {
        return $this->patterns;
    }

    /**
     * Set a global where pattern on all routes.
     *
     * @param string $key
     * @param string $pattern
     *
     * @return void
     */
    public function pattern($key, $pattern) {
        $this->patterns[$key] = $pattern;
    }

    /**
     * Set a group of global where patterns on all routes.
     *
     * @param array $patterns
     *
     * @return void
     */
    public function patterns($patterns) {
        foreach ($patterns as $key => $pattern) {
            $this->pattern($key, $pattern);
        }
    }

    /**
     * Determine if the router currently has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack() {
        return !empty($this->groupStack);
    }

    /**
     * Get the current group stack for the router.
     *
     * @return array
     */
    public function getGroupStack() {
        return $this->groupStack;
    }

    /**
     * Get a route parameter for the current route.
     *
     * @param string      $key
     * @param null|string $default
     *
     * @return mixed
     */
    public function input($key, $default = null) {
        return $this->current()->parameter($key, $default);
    }

    /**
     * Get the request currently being dispatched.
     *
     * @return CHTTP_Request
     */
    public function getCurrentRequest() {
        return $this->currentRequest;
    }

    /**
     * Get the currently dispatched route instance.
     *
     * @return null|CRouting_Route
     */
    public function getCurrentRoute() {
        return $this->current();
    }

    /**
     * Get the currently dispatched route instance.
     *
     * @return null|CRouting_Route
     */
    public function current() {
        return $this->current;
    }

    /**
     * Check if a route with the given name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name) {
        $names = is_array($name) ? $name : func_get_args();

        foreach ($names as $value) {
            if (!$this->routes->hasNamedRoute($value)) {
                return false;
            }
        }

        return true;
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
     * @param mixed ...$patterns
     *
     * @return bool
     */
    public function is(...$patterns) {
        return $this->currentRouteNamed(...$patterns);
    }

    /**
     * Determine if the current route matches a pattern.
     *
     * @param mixed ...$patterns
     *
     * @return bool
     */
    public function currentRouteNamed(...$patterns) {
        return $this->current() && $this->current()->named(...$patterns);
    }

    /**
     * Get the current route action.
     *
     * @return null|string
     */
    public function currentRouteAction() {
        if ($this->current()) {
            return carr::get($this->current()->getAction(), 'controller', null);
        }
    }

    /**
     * Alias for the "currentRouteUses" method.
     *
     * @param array ...$patterns
     *
     * @return bool
     */
    public function uses(...$patterns) {
        foreach ($patterns as $pattern) {
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
     * Get the underlying route collection.
     *
     * @return CRouting_RouteCollectionInterface
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Set the route collection instance.
     *
     * @param CRouting_RouteCollection $routes
     *
     * @return void
     */
    public function setRoutes(CRouting_RouteCollection $routes) {
        foreach ($routes as $route) {
            $route->setRouter($this)->setContainer($this->container);
        }

        $this->routes = $routes;

        $this->container->instance('routes', $this->routes);
    }

    /**
     * Set the compiled route collection instance.
     *
     * @param array $routes
     *
     * @return void
     */
    public function setCompiledRoutes(array $routes) {
        $this->routes = (new CRouting_CompiledRouteCollection($routes['compiled'], $routes['attributes']))
            ->setRouter($this)
            ->setContainer($this->container);

        $this->container->instance('routes', $this->routes);
    }

    /**
     * Remove any duplicate middleware from the given array.
     *
     * @param array $middleware
     *
     * @return array
     */
    public static function uniqueMiddleware(array $middleware) {
        $seen = [];
        $result = [];

        foreach ($middleware as $value) {
            $key = \is_object($value) ? \spl_object_id($value) : $value;

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Dynamically handle calls into the router instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $routeRegistrar = new CRouting_RouteRegistrar($this);
        if ($method === 'middleware') {
            return $routeRegistrar->attribute($method, is_array($parameters[0]) ? $parameters[0] : $parameters);
        }

        return $routeRegistrar->attribute($method, $parameters[0]);
    }

    /**
     * @param string         $uri
     * @param string|Closure $routedUri
     *
     * @return $this
     */
    public function addUriRouting($uri, $routedUri) {
        CRouting_Manager::instance()->addUriRouting($uri, $routedUri);

        return $this;
    }
}
