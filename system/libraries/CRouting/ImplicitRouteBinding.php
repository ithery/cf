<?php
class CRouting_ImplicitRouteBinding {
    /**
     * Resolve the implicit route bindings for the given route.
     *
     * @param CContainer_Container $container
     * @param CRouting_Route       $route
     *
     * @throws CModel_Exception_ModelNotFoundException
     *
     * @return void
     */
    public static function resolveForRoute($container, $route) {
        $parameters = $route->parameters();

        foreach ($route->signatureParameters(CRouting_UrlRoutableInterface::class) as $parameter) {
            if (!$parameterName = static::getParameterName($parameter->getName(), $parameters)) {
                continue;
            }

            $parameterValue = $parameters[$parameterName];

            if ($parameterValue instanceof CRouting_UrlRoutableInterface) {
                continue;
            }

            $instance = $container->make(CBase_Reflector::getParameterClassName($parameter));

            $parent = $route->parentOfParameter($parameterName);

            if ($parent instanceof CRouting_UrlRoutableInterface && in_array($parameterName, array_keys($route->bindingFields()))) {
                $model = $parent->resolveChildRouteBinding(
                    $parameterName,
                    $parameterValue,
                    $route->bindingFieldFor($parameterName)
                );
                if (!$model) {
                    throw (new CModel_Exception_ModelNotFoundException())->setModel(get_class($instance), [$parameterValue]);
                }
            } elseif (!$model = $instance->resolveRouteBinding($parameterValue, $route->bindingFieldFor($parameterName))) {
                throw (new CModel_Exception_ModelNotFoundException())->setModel(get_class($instance), [$parameterValue]);
            }

            $route->setParameter($parameterName, $model);
        }
    }

    /**
     * Return the parameter name if it exists in the given parameters.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return null|string
     */
    protected static function getParameterName($name, $parameters) {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = cstr::snake($name), $parameters)) {
            return $snakedName;
        }
    }
}
