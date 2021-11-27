<?php

class CRouting_RouteFileRegistrar {
    /**
     * The router instance.
     *
     * @var \CRouting_Router
     */
    protected $router;

    /**
     * Create a new route file registrar instance.
     *
     * @param \CRouting_Router $router
     *
     * @return void
     */
    public function __construct(CRouting_Router $router) {
        $this->router = $router;
    }

    /**
     * Require the given routes file.
     *
     * @param string $routes
     *
     * @return void
     */
    public function register($routes) {
        $router = $this->router;

        require $routes;
    }
}
