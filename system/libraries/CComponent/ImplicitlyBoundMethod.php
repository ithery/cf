<?php

defined('SYSPATH') or die('No direct access allowed.');

use CRouting_UrlRoutableInterface as ImplicitlyBindable;

class CComponent_ImplicitlyBoundMethod extends CContainer_BoundMethod {
    protected static function getMethodDependencies($container, $callback, array $parameters = []) {
        $dependencies = [];
        $paramIndex = 0;

        foreach (static::getCallReflector($callback)->getParameters() as $parameter) {
            static::substituteNameBindingForCallParameter($parameter, $parameters, $paramIndex);
            static::substituteImplicitBindingForCallParameter($container, $parameter, $parameters);
            static::addDependencyForCallParameter($container, $parameter, $parameters, $dependencies);
        }

        return array_merge($dependencies, $parameters);
    }

    protected static function substituteNameBindingForCallParameter($parameter, array &$parameters, &$paramIndex) {
        // check if we have a candidate for name/value binding
        if (!array_key_exists($paramIndex, $parameters)) {
            return;
        }

        if ($parameter->isVariadic()) {
            // this last param will pick up the rest - reindex any remaining parameters
            $parameters = array_merge(
                array_filter($parameters, function ($key) {
                    return !is_int($key);
                }, ARRAY_FILTER_USE_KEY),
                array_values(array_filter($parameters, function ($key) {
                    return is_int($key);
                }, ARRAY_FILTER_USE_KEY))
            );

            return;
        }

        // stop if this one is due for dependency injection
        if (!is_null($className = static::getClassForDependencyInjection($parameter)) && !$parameters[$paramIndex] instanceof $className) {
            return;
        }

        if (!array_key_exists($paramName = $parameter->getName(), $parameters)) {
            // have a parameter value that is bound by sequential order
            // and not yet bound by name, so bind it to parameter name

            $parameters[$paramName] = $parameters[$paramIndex];
            unset($parameters[$paramIndex]);
            $paramIndex++;
        }
    }

    protected static function substituteImplicitBindingForCallParameter($container, $parameter, array &$parameters) {
        $paramName = $parameter->getName();

        // check if we have a candidate for implicit binding
        if (is_null($className = static::getClassForImplicitBinding($parameter))) {
            return;
        }

        // Check if the value we have for this param is an instance
        // of the desired class, attempt implicit binding if not
        if (array_key_exists($paramName, $parameters) && !$parameters[$paramName] instanceof $className) {
            $parameters[$paramName] = static::getImplicitBinding($container, $className, $parameters[$paramName]);
        } elseif (array_key_exists($className, $parameters) && !$parameters[$className] instanceof $className) {
            $parameters[$className] = static::getImplicitBinding($container, $className, $parameters[$className]);
        }
    }

    protected static function getClassForDependencyInjection($parameter) {
        if (!is_null($className = static::getParameterClassName($parameter)) && !static::implementsInterface($parameter)) {
            return $className;
        }
    }

    protected static function getClassForImplicitBinding($parameter) {
        if (!is_null($className = static::getParameterClassName($parameter)) && static::implementsInterface($parameter)) {
            return $className;
        }

        return null;
    }

    protected static function getImplicitBinding($container, $className, $value) {
        $model = $container->make($className)->resolveRouteBinding($value);

        if (!$model) {
            throw (new CModel_Exception_ModelNotFoundException())->setModel($className, [$value]);
        }

        return $model;
    }

    public static function getParameterClassName($parameter) {
        if (method_exists($parameter, 'getType')) {
            $type = $parameter->getType();

            return ($type && !$type->isBuiltin()) ? $type->getName() : null;
        }

        return !static::getParameterIsBuiltIn($parameter) ? static::getParameterTypeName($parameter) : null;
    }

    public static function implementsInterface($parameter) {
        $typeName = static::getParameterTypeName($parameter);

        return (new ReflectionClass($typeName))->implementsInterface(ImplicitlyBindable::class);
    }

    public static function getParameterTypeName($parameter) {
        if (method_exists($parameter, 'getType')) {
            return $parameter->getType()->getName();
        }

        return static::getParameterTypeNameForPhp56($parameter);
    }

    public static function getParameterIsBuiltIn($parameter) {
        if (method_exists($parameter, 'getType')) {
            return $parameter->getType()->isBuiltin();
        }

        return static::getParameterTypeIsBuiltInForPhp56($parameter);
    }

    public static function getParameterTypeNameForPhp56($parameter) {
        $type = gettype($parameter);

        return $type;
    }

    public static function getParameterTypeIsBuiltInForPhp56($parameter) {
        $builtInType = ['boolean', 'integer', 'double', 'string', 'array', 'object', 'NULL', 'resource', 'resource (closed)', 'unknown type'];

        return in_array(static::getParameterTypeNameForPhp56($parameter), $builtInType);
    }
}
