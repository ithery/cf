<?php

trait CTrait_ReflectsClosureTrait {
    /**
     * Get the class name of the first parameter of the given Closure.
     *
     * @param \Closure $closure
     *
     * @throws \ReflectionException|\RuntimeException
     *
     * @return string
     */
    protected function firstClosureParameterType(Closure $closure) {
        $types = array_values($this->closureParameterTypes($closure));

        if (!$types) {
            throw new RuntimeException('The given Closure has no parameters.');
        }

        if ($types[0] === null) {
            throw new RuntimeException('The first parameter of the given Closure is missing a type hint.');
        }

        return $types[0];
    }

    /**
     * Get the class names of the first parameter of the given Closure, including union types.
     *
     * @param \Closure $closure
     *
     * @throws \ReflectionException
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function firstClosureParameterTypes(Closure $closure) {
        $reflection = new ReflectionFunction($closure);

        $types = c::collect($reflection->getParameters())->mapWithKeys(function ($parameter) {
            if ($parameter->isVariadic()) {
                return [$parameter->getName() => null];
            }
            if (method_exists(Reflector::class, 'getParameterClassNames')) {
                return [$parameter->getName() => Reflector::getParameterClassNames($parameter)];
            }
            $typeName = null;
            $type = $parameter->getType();
            if ($type !== null) {
                if ($type->isBuiltin()) {
                    $typeName = $type->getName();
                } elseif ($type->isClass() && class_exists($type->getName())) {
                    $typeName = $type->getName();
                }
            }

            return [$parameter->getName() => $typeName];
        })->filter()->values()->all();

        if (empty($types)) {
            throw new RuntimeException('The given Closure has no parameters.');
        }

        if (isset($types[0]) && empty($types[0])) {
            throw new RuntimeException('The first parameter of the given Closure is missing a type hint.');
        }

        return $types[0];
    }

    /**
     * Get the class names / types of the parameters of the given Closure.
     *
     * @param \Closure $closure
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    protected function closureParameterTypes(Closure $closure) {
        $reflection = new ReflectionFunction($closure);

        return c::collect($reflection->getParameters())->mapWithKeys(function ($parameter) {
            if ($parameter->isVariadic()) {
                return [$parameter->getName() => null];
            }

            return [$parameter->getName() => $parameter->getClass()->getName()];
        })->all();
    }
}
