<?php

class CApi_Routing_RouteCollection implements Countable, IteratorAggregate {
    /**
     * Routes on the collection.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Lookup for named routes.
     *
     * @var array
     */
    protected $names = [];

    /**
     * Lookup for action routes.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Add a route to the collection.
     *
     * @param \CApi_Routing_Route $route
     *
     * @return \CApi_Routing_Route
     */
    public function add(CApi_Routing_Route $route) {
        $this->routes[] = $route;

        $this->addLookups($route);

        return $route;
    }

    /**
     * Add route lookups.
     *
     * @param \CApi_Routing_Route $route
     *
     * @return void
     */
    protected function addLookups(CApi_Routing_Route $route) {
        $action = $route->getAction();

        if (isset($action['as'])) {
            $this->names[$action['as']] = $route;
        }

        if (isset($action['controller'])) {
            $this->actions[$action['controller']] = $route;
        }
    }

    /**
     * Get a route by name.
     *
     * @param string $name
     *
     * @return null|\CApi_Routing_Route
     */
    public function getByName($name) {
        return isset($this->names[$name]) ? $this->names[$name] : null;
    }

    /**
     * Get a route by action.
     *
     * @param string $action
     *
     * @return null|\CApi_Routing_Route
     */
    public function getByAction($action) {
        return isset($this->actions[$action]) ? $this->actions[$action] : null;
    }

    /**
     * Get all routes.
     *
     * @return array
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->getRoutes());
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count() {
        return count($this->getRoutes());
    }
}
