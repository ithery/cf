<?php

/**
 * Description of RouteCollectionAbstract
 *
 * @author Hery
 */
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;

abstract class CRouting_RouteCollectionAbstract implements Countable, IteratorAggregate, CRouting_RouteCollectionInterface {
    /**
     * Handle the matched route.
     *
     * @param CHTTP_Request       $request
     * @param CRouting_Route|null $route
     *
     * @return CRouting_Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function handleMatchedRoute(CHTTP_Request $request, $route) {
        if (!is_null($route)) {
            return $route->bind($request);
        }

        // If no route was found we will now check if a matching route is specified by
        // another HTTP verb. If it is we will need to throw a MethodNotAllowed and
        // inform the user agent of which HTTP verb it should use for this route.
        $others = $this->checkForAlternateVerbs($request);

        if (count($others) > 0) {
            return $this->getRouteForMethods($request, $others);
        }

        throw new NotFoundHttpException;
    }

    /**
     * Determine if any routes match on another HTTP verb.
     *
     * @param CHTTP_Request $request
     *
     * @return array
     */
    protected function checkForAlternateVerbs($request) {
        $methods = array_diff(CRouting_Router::$verbs, [$request->getMethod()]);

        // Here we will spin through all verbs except for the current request verb and
        // check to see if any routes respond to them. If they do, we will return a
        // proper error response with the correct headers on the response string.
        return array_values(array_filter(
            $methods,
            function ($method) use ($request) {
                return !is_null($this->matchAgainstRoutes($this->get($method), $request, false));
            }
        ));
    }

    /**
     * Determine if a route in the array matches the request.
     *
     * @param CRouting_Route[] $routes
     * @param CHTTP_Request    $request
     * @param bool             $includingMethod
     *
     * @return CRouting_Route|null
     */
    protected function matchAgainstRoutes(array $routes, $request, $includingMethod = true) {
        list($fallbacks, $routes) = c::collect($routes)->partition(function ($route) {
            return $route->isFallback;
        });

        return $routes->merge($fallbacks)->first(function (CRouting_Route $route) use ($request, $includingMethod) {
            return $route->matches($request, $includingMethod);
        });
    }

    /**
     * Get a route (if necessary) that responds when other available methods are present.
     *
     * @param CHTTP_Request $request
     * @param string[]      $methods
     *
     * @return CRouting_Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function getRouteForMethods($request, array $methods) {
        if ($request->method() === 'OPTIONS') {
            return (new CRouting_Route('OPTIONS', $request->path(), function () use ($methods) {
                return new CHTTP_Response('', 200, ['Allow' => implode(',', $methods)]);
            }))->bind($request);
        }

        $this->methodNotAllowed($methods, $request->method());
    }

    /**
     * Throw a method not allowed HTTP exception.
     *
     * @param array  $others
     * @param string $method
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function methodNotAllowed(array $others, $method) {
        throw new MethodNotAllowedHttpException(
            $others,
            sprintf(
                'The %s method is not supported for this route. Supported methods: %s.',
                $method,
                implode(', ', $others)
            )
        );
    }

    /**
     * Compile the routes for caching.
     *
     * @return array
     */
    public function compile() {
        $compiled = $this->dumper()->getCompiledRoutes();

        $attributes = [];

        foreach ($this->getRoutes() as $route) {
            $attributes[$route->getName()] = [
                'methods' => $route->methods(),
                'uri' => $route->uri(),
                'action' => $route->getAction(),
                'fallback' => $route->isFallback,
                'defaults' => $route->defaults,
                'wheres' => $route->wheres,
                'bindingFields' => $route->bindingFields(),
                'lockSeconds' => $route->locksFor(),
                'waitSeconds' => $route->waitsFor(),
            ];
        }

        return compact('compiled', 'attributes');
    }

    /**
     * Return the CompiledUrlMatcherDumper instance for the route collection.
     *
     * @return \Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper
     */
    public function dumper() {
        return new CompiledUrlMatcherDumper($this->toSymfonyRouteCollection());
    }

    /**
     * Convert the collection to a Symfony RouteCollection instance.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function toSymfonyRouteCollection() {
        $symfonyRoutes = new SymfonyRouteCollection;

        $routes = $this->getRoutes();

        foreach ($routes as $route) {
            if (!$route->isFallback) {
                $symfonyRoutes = $this->addToSymfonyRoutesCollection($symfonyRoutes, $route);
            }
        }

        foreach ($routes as $route) {
            if ($route->isFallback) {
                $symfonyRoutes = $this->addToSymfonyRoutesCollection($symfonyRoutes, $route);
            }
        }

        return $symfonyRoutes;
    }

    /**
     * Add a route to the SymfonyRouteCollection instance.
     *
     * @param \Symfony\Component\Routing\RouteCollection $symfonyRoutes
     * @param CRouting_Route                             $route
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    protected function addToSymfonyRoutesCollection(SymfonyRouteCollection $symfonyRoutes, CRouting_Route $route) {
        $name = $route->getName();

        if (cstr::endsWith($name, '.')
            && !is_null($symfonyRoutes->get($name))
        ) {
            $name = null;
        }

        if (!$name) {
            $route->name($name = $this->generateRouteName());

            $this->add($route);
        } elseif (!is_null($symfonyRoutes->get($name))) {
            throw new LogicException("Unable to prepare route [{$route->uri}] for serialization. Another route has already been assigned name [{$name}].");
        }

        $symfonyRoutes->add($route->getName(), $route->toSymfonyRoute());

        return $symfonyRoutes;
    }

    /**
     * Get a randomly generated route name.
     *
     * @return string
     */
    protected function generateRouteName() {
        return 'generated::' . cstr::random();
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
    #[\ReturnTypeWillChange]
    public function count() {
        return count($this->getRoutes());
    }
}
