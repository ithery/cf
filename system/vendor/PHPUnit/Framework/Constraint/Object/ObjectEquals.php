<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint\Object;

use function get_class;
use function is_object;
use PHPUnit\Framework\Exception\ActualValueIsNotAnObjectException;
use PHPUnit\Framework\Exception\ComparisonMethodDoesNotAcceptParameterTypeException;
use PHPUnit\Framework\Exception\ComparisonMethodDoesNotDeclareBoolReturnTypeException;
use PHPUnit\Framework\Exception\ComparisonMethodDoesNotDeclareExactlyOneParameterException;
use PHPUnit\Framework\Exception\ComparisonMethodDoesNotDeclareParameterTypeException;
use PHPUnit\Framework\Exception\ComparisonMethodDoesNotExistException;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionNamedType;
use ReflectionObject;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class ObjectEquals extends Constraint
{
    /**
     * @var object
     */
    private $expected;

    /**
     * @var string
     */
    private $method;

    public function __construct(object $object, $method = 'equals')
    {
        $this->expected = $object;
        $this->method   = $method;
    }

    public function toString()
    {
        return 'two objects are equal';
    }

    /**
     * @throws ActualValueIsNotAnObjectException
     * @throws ComparisonMethodDoesNotExistException
     * @throws ComparisonMethodDoesNotDeclareBoolReturnTypeException
     * @throws ComparisonMethodDoesNotDeclareExactlyOneParameterException
     * @throws ComparisonMethodDoesNotDeclareParameterTypeException
     * @throws ComparisonMethodDoesNotAcceptParameterTypeException
     */
    protected function matches($other)
    {
        if (!is_object($other)) {
            throw new ActualValueIsNotAnObjectException;
        }

        $object = new ReflectionObject($other);

        if (!$object->hasMethod($this->method)) {
            throw new ComparisonMethodDoesNotExistException(
                get_class($other),
                $this->method
            );
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $method = $object->getMethod($this->method);

        if (!$method->hasReturnType()) {
            throw new ComparisonMethodDoesNotDeclareBoolReturnTypeException(
                get_class($other),
                $this->method
            );
        }

        $returnType = $method->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            throw new ComparisonMethodDoesNotDeclareBoolReturnTypeException(
                get_class($other),
                $this->method
            );
        }

        if ($returnType->allowsNull()) {
            throw new ComparisonMethodDoesNotDeclareBoolReturnTypeException(
                get_class($other),
                $this->method
            );
        }

        if ($returnType->getName() !== 'bool') {
            throw new ComparisonMethodDoesNotDeclareBoolReturnTypeException(
                get_class($other),
                $this->method
            );
        }

        if ($method->getNumberOfParameters() !== 1 || $method->getNumberOfRequiredParameters() !== 1) {
            throw new ComparisonMethodDoesNotDeclareExactlyOneParameterException(
                get_class($other),
                $this->method
            );
        }

        $parameter = $method->getParameters()[0];

        if (!$parameter->hasType()) {
            throw new ComparisonMethodDoesNotDeclareParameterTypeException(
                get_class($other),
                $this->method
            );
        }

        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType) {
            throw new ComparisonMethodDoesNotDeclareParameterTypeException(
                get_class($other),
                $this->method
            );
        }

        $typeName = $type->getName();

        if ($typeName === 'self') {
            $typeName = get_class($other);
        }

        if (!$this->expected instanceof $typeName) {
            throw new ComparisonMethodDoesNotAcceptParameterTypeException(
                get_class($other),
                $this->method,
                get_class($this->expected)
            );
        }

        return $other->{$this->method}($this->expected);
    }

    protected function failureDescription($other)
    {
        return $this->toString();
    }
}
