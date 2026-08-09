<?php

class CRouting_RouteFinder {
    /**
     * Maps verb prefixes to their allowed HTTP methods.
     *
     * @var array<string, string[]>
     */
    protected static $verbMap = [
        'get' => ['GET', 'HEAD'],
        'post' => ['POST'],
        'put' => ['PUT'],
        'patch' => ['PATCH'],
        'delete' => ['DELETE'],
        'options' => ['OPTIONS'],
    ];

    /**
     * Return Route From Uri.
     *
     * @param string $uri
     *
     * @return null|CRouting_Route
     */
    public static function find($uri = null) {
        if ($uri == null) {
            $uri = PHP_SAPI == 'cli' ? CRouting_UrlFinder::getUri() : CHTTP::request()->path();

            $uri = trim($uri, '/');
        }

        $routeData = new CRouting_RouteData($uri);

        CFRouter::applyRouteData($routeData);

        $className = $routeData->getControllerClass();
        $methodSegment = $routeData->getMethod();

        $route = null;

        if (class_exists($className)) {
            $resolved = static::resolveMethodAndVerbs($className, $methodSegment);

            if ($resolved !== null) {
                list($actualMethod, $verbs) = $resolved;

                if (static::isRoutableMethod($className, $actualMethod)) {
                    $routedUri = strtolower($routeData->getControllerDir()) . $routeData->getController();
                    // Strip { and } before splicing into the pattern - a raw method segment
                    // containing them (e.g. a scanner probe with "${IFS}" repeated) gets misread
                    // by Symfony's route compiler as a {varname} token and throws. $methodSegment
                    // itself (used below via $actualMethod for the real dispatch) is untouched.
                    $routedUri .= '/' . str_replace(['{', '}'], '', $methodSegment);

                    $arguments = $routeData->getArguments();

                    $parameters = [];
                    foreach ($arguments as $key => $argument) {
                        $routedUri .= "/{any" . $key . "}";
                        $parameters[$key] = $argument;
                    }

                    $route = new CRouting_Route($verbs, $routedUri, $className . '@' . $actualMethod, $parameters);

                    $route->setRouteData($routeData);
                }
            }
        }

        return $route;
    }

    /**
     * Resolve the actual method name and allowed HTTP verbs for a URL segment.
     *
     * Supports verb-prefixed methods (getDetail, postStore, etc.)
     * and falls back to unprefixed methods for backward compatibility.
     *
     * @param string $className
     * @param string $methodSegment
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     *
     * @return null|array{0: string, 1: string[]}
     */
    protected static function resolveMethodAndVerbs($className, $methodSegment) {
        $httpMethod = PHP_SAPI === 'cli' ? 'get' : strtolower(CHTTP::request()->method());

        // HEAD requests should resolve via GET-prefixed methods
        if ($httpMethod === 'head') {
            $httpMethod = 'get';
        }

        $ucSegment = ucfirst($methodSegment);

        // 1. Try verb-prefixed method for current HTTP method (e.g. postStore)
        if (isset(static::$verbMap[$httpMethod])) {
            $prefixed = $httpMethod . $ucSegment;
            if (method_exists($className, $prefixed)) {
                return [$prefixed, static::$verbMap[$httpMethod]];
            }
        }

        // 2. Try "any" prefix (e.g. anyStore)
        $anyMethod = 'any' . $ucSegment;
        if (method_exists($className, $anyMethod)) {
            return [$anyMethod, CRouting_Router::$verbs];
        }

        // 3. Try unprefixed method — backward compatible, all verbs
        if (method_exists($className, $methodSegment)) {
            return [$methodSegment, CRouting_Router::$verbs];
        }

        // 4. Controller has __call — acts as catch-all for undefined methods
        if (method_exists($className, '__call')) {
            return [$methodSegment, CRouting_Router::$verbs];
        }

        // 5. Check if other verb-prefixed methods exist → 405 Method Not Allowed
        $allowedVerbs = static::getAllowedVerbs($className, $methodSegment);
        if (!empty($allowedVerbs)) {
            throw new Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException(
                $allowedVerbs,
                sprintf(
                    'The %s method is not supported for route [%s]. Supported methods: %s.',
                    strtoupper($httpMethod),
                    $methodSegment,
                    implode(', ', $allowedVerbs)
                )
            );
        }

        return null;
    }

    /**
     * Get all HTTP verbs that have verb-prefixed methods for the given segment.
     *
     * @param string $className
     * @param string $methodSegment
     *
     * @return string[]
     */
    protected static function getAllowedVerbs($className, $methodSegment) {
        $ucSegment = ucfirst($methodSegment);
        $allowed = [];

        foreach (static::$verbMap as $prefix => $verbs) {
            if (method_exists($className, $prefix . $ucSegment)) {
                $allowed = array_merge($allowed, $verbs);
            }
        }

        return array_unique($allowed);
    }

    /**
     * @param string $className
     * @param string $method
     *
     * @return bool
     */
    protected static function isRoutableMethod($className, $method) {
        if (substr($method, 0, 2) === '__') {
            return false;
        }

        if (!method_exists($className, $method)) {
            return method_exists($className, '__call');
        }

        $reflection = new ReflectionMethod($className, $method);

        if (!$reflection->isPublic()) {
            return false;
        }

        // Block methods inherited from CController that are not actions
        $declaringClass = $reflection->getDeclaringClass()->getName();
        if ($declaringClass === 'CController') {
            return false;
        }

        return true;
    }

    /**
     * @param string $controller
     *
     * @return string
     */
    public static function controllerUrl($controller) {
        $classExplode = explode('_', $controller);
        $classExplode = array_map(function ($item) {
            return cstr::camel($item);
        }, $classExplode);
        $url = curl::base() . implode('/', array_slice($classExplode, 1)) . '/';

        return $url;
    }
}
