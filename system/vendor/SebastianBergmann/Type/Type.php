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

use const PHP_VERSION;
use function get_class;
use function gettype;
use function strtolower;
use function version_compare;

abstract class Type
{
    public static function fromValue( $value,  $allowsNull)
    {
        if ($value === false) {
            return new FalseType;
        }

        $typeName = gettype($value);

        if ($typeName === 'object') {
            return new ObjectType(TypeName::fromQualifiedName(get_class($value)), $allowsNull);
        }

        $type = self::fromName($typeName, $allowsNull);

        if ($type instanceof SimpleType) {
            $type = new SimpleType($typeName, $allowsNull, $value);
        }

        return $type;
    }

    public static function fromName( $typeName,  $allowsNull)
    {
        if (version_compare(PHP_VERSION, '8.1.0-dev', '>=') && strtolower($typeName) === 'never') {
            return new NeverType;
        }

        return match (strtolower($typeName)) {
            'callable'     => new CallableType($allowsNull),
            'false'        => new FalseType,
            'iterable'     => new IterableType($allowsNull),
            'null'         => new NullType,
            'object'       => new GenericObjectType($allowsNull),
            'unknown type' => new UnknownType,
            'void'         => new VoidType,
            'array', 'bool', 'boolean', 'double', 'float', 'int', 'integer', 'real', 'resource', 'resource (closed)', 'string' => new SimpleType($typeName, $allowsNull),
            default => new ObjectType(TypeName::fromQualifiedName($typeName), $allowsNull),
        };
    }

    public function asString()
    {
        return ($this->allowsNull() ? '?' : '') . $this->name();
    }

    public function isCallable()
    {
        return false;
    }

    public function isFalse()
    {
        return false;
    }

    public function isGenericObject()
    {
        return false;
    }

    public function isIntersection()
    {
        return false;
    }

    public function isIterable()
    {
        return false;
    }

    public function isMixed()
    {
        return false;
    }

    public function isNever()
    {
        return false;
    }

    public function isNull()
    {
        return false;
    }

    public function isObject()
    {
        return false;
    }

    public function isSimple()
    {
        return false;
    }

    public function isStatic()
    {
        return false;
    }

    public function isUnion()
    {
        return false;
    }

    public function isUnknown()
    {
        return false;
    }

    public function isVoid()
    {
        return false;
    }

    abstract public function isAssignable(self $other);

    abstract public function name();

    abstract public function allowsNull();
}
