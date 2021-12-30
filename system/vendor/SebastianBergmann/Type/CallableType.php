<?php
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Type;

use Closure;
use function count;
use function assert;
use ReflectionClass;
use function explode;
use ReflectionObject;
use function is_array;
use function is_object;
use function is_string;
use ReflectionException;
use function class_exists;
use function str_contains;
use function function_exists;

final class CallableType extends Type {
    private $allowsNull;

    public function __construct($nullable) {
        $this->allowsNull = $nullable;
    }

    /**
     * @throws RuntimeException
     */
    public function isAssignable(Type $other) {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($other instanceof self) {
            return true;
        }

        if ($other instanceof ObjectType) {
            if ($this->isClosure($other)) {
                return true;
            }

            if ($this->hasInvokeMethod($other)) {
                return true;
            }
        }

        if ($other instanceof SimpleType) {
            if ($this->isFunction($other)) {
                return true;
            }

            if ($this->isClassCallback($other)) {
                return true;
            }

            if ($this->isObjectCallback($other)) {
                return true;
            }
        }

        return false;
    }

    public function name() {
        return 'callable';
    }

    public function allowsNull() {
        return $this->allowsNull;
    }

    public function isCallable(): bool {
        return true;
    }

    private function isClosure(ObjectType $type) {
        return !$type->className()->isNamespaced() && $type->className()->simpleName() === Closure::class;
    }

    /**
     * @throws RuntimeException
     */
    private function hasInvokeMethod(ObjectType $type) {
        $className = $type->className()->qualifiedName();
        assert(class_exists($className));

        try {
            $class = new ReflectionClass($className);
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
            // @codeCoverageIgnoreEnd
        }

        if ($class->hasMethod('__invoke')) {
            return true;
        }

        return false;
    }

    private function isFunction(SimpleType $type) {
        if (!is_string($type->value())) {
            return false;
        }

        return function_exists($type->value());
    }

    private function isObjectCallback(SimpleType $type) {
        if (!is_array($type->value())) {
            return false;
        }

        if (count($type->value()) !== 2) {
            return false;
        }

        if (!is_object($type->value()[0]) || !is_string($type->value()[1])) {
            return false;
        }

        list($object, $methodName) = $type->value();

        return (new ReflectionObject($object))->hasMethod($methodName);
    }

    private function isClassCallback(SimpleType $type) {
        if (!is_string($type->value()) && !is_array($type->value())) {
            return false;
        }

        if (is_string($type->value())) {
            if (!str_contains($type->value(), '::')) {
                return false;
            }

            list($className, $methodName) = explode('::', $type->value());
        }

        if (is_array($type->value())) {
            if (count($type->value()) !== 2) {
                return false;
            }

            if (!is_string($type->value()[0]) || !is_string($type->value()[1])) {
                return false;
            }

            list($className, $methodName) = $type->value();
        }

        assert(isset($className) && is_string($className) && class_exists($className));
        assert(isset($methodName) && is_string($methodName));

        try {
            $class = new ReflectionClass($className);

            if ($class->hasMethod($methodName)) {
                $method = $class->getMethod($methodName);

                return $method->isPublic() && $method->isStatic();
            }
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
            // @codeCoverageIgnoreEnd
        }

        return false;
    }
}
