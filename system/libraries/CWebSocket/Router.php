<?php

use Ratchet\WebSocket\WsServer;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Ratchet\WebSocket\MessageComponentInterface;

class CWebSocket_Router {
    /**
     * The implemented routes.
     *
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routes;

    /**
     * Define the custom routes.
     *
     * @var array
     */
    protected $customRoutes;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Initialize the class.
     *
     * @return void
     */
    private function __construct() {
        $this->routes = new RouteCollection();

        $this->customRoutes = [
            'get' => new CCollection(),
            'post' => new CCollection(),
            'put' => new CCollection(),
            'patch' => new CCollection(),
            'delete' => new CCollection(),
        ];
    }

    /**
     * Get the routes.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Get the list of routes that still need to be registered.
     *
     * @return array[CCollection]
     */
    public function getCustomRoutes() {
        return $this->customRoutes;
    }

    /**
     * Register the default routes.
     *
     * @return void
     */
    public function registerRoutes() {
        $this->get('/app/{appKey}', CF::config('websocket.handlers.websocket'));
        $this->post('/apps/{appId}/events', CF::config('websocket.handlers.trigger_event'));
        $this->get('/apps/{appId}/channels', CF::config('websocket.handlers.fetch_channels'));
        $this->get('/apps/{appId}/channels/{channelName}', CF::config('websocket.handlers.fetch_channel'));
        $this->get('/apps/{appId}/channels/{channelName}/users', CF::config('websocket.handlers.fetch_users'));
        $this->get('/health', CF::config('websocket.handlers.health'));
        $this->registerCustomRoutes();
    }

    /**
     * Add a GET route.
     *
     * @param string $uri
     * @param string $action
     *
     * @return void
     */
    public function get($uri, $action) {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Add a POST route.
     *
     * @param string $uri
     * @param string $action
     *
     * @return void
     */
    public function post($uri, $action) {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Add a PUT route.
     *
     * @param string $uri
     * @param string $action
     *
     * @return void
     */
    public function put($uri, $action) {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Add a PATCH route.
     *
     * @param string $uri
     * @param string $action
     *
     * @return void
     */
    public function patch($uri, $action) {
        $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Add a DELETE route.
     *
     * @param string $uri
     * @param string $action
     *
     * @return void
     */
    public function delete($uri, $action) {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add a new route to the list.
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     *
     * @return void
     */
    public function addRoute($method, $uri, $action) {
        $this->routes->add($uri, $this->getRoute($method, $uri, $action));
    }

    /**
     * Add a new custom route. Registered routes
     * will be resolved at server spin-up.
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     *
     * @return void
     */
    public function addCustomRoute($method, $uri, $action) {
        $this->customRoutes[strtolower($method)]->put($uri, $action);
    }

    /**
     * Register the custom routes into the main RouteCollection.
     *
     * @return void
     */
    public function registerCustomRoutes() {
        foreach ($this->customRoutes as $method => $actions) {
            $actions->each(function ($action, $uri) use ($method) {
                $this->{$method}($uri, $action);
            });
        }
    }

    /**
     * Get the route of a specified method, uri and action.
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     *
     * @return \Symfony\Component\Routing\Route
     */
    protected function getRoute($method, $uri, $action) {
        $action = is_subclass_of($action, MessageComponentInterface::class)
            ? $this->createWebSocketsServer($action)
            : $this->createAction($action);

        return new Route($uri, ['_controller' => $action], [], [], null, [], [$method]);
    }

    /**
     * Create a new websockets server to handle the action.
     *
     * @param string $action
     *
     * @return \Ratchet\WebSocket\WsServer
     */
    protected function createWebSocketsServer($action) {
        $app = $this->createAction($action);

        if (CWebSocket_Server_Logger_WebSocketLogger::isEnabled()) {
            $app = CWebSocket_Server_Logger_WebSocketLogger::decorate($app);
        }

        return new WsServer($app);
    }

    public static function createAction($action) {
        if (!class_exists($action)) {
            throw new Exception('class ' . $action . ' not exists');
        }

        return new $action();
    }
}
