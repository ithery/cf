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

use function strtolower;

final class SimpleType extends Type
{
    private  $name;

    private  $allowsNull;

    private  $value;

    public function __construct( $name,  $nullable, $value = null)
    {
        $this->name       = $this->normalize($name);
        $this->allowsNull = $nullable;
        $this->value      = $value;
    }

    public function isAssignable(Type $other)
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($this->name === 'bool' && $other->name() === 'false') {
            return true;
        }

        if ($other instanceof self) {
            return $this->name === $other->name;
        }

        return false;
    }

    public function name()
    {
        return $this->name;
    }

    public function allowsNull()
    {
        return $this->allowsNull;
    }

    public function value()
    {
        return $this->value;
    }

    public function isSimple()
    {
        return true;
    }

    private function normalize( $name)
    {
        $name = strtolower($name);

        return match ($name) {
            'boolean' => 'bool',
            'real', 'double' => 'float',
            'integer' => 'int',
            '[]'      => 'array',
            default   => $name,
        };
    }
}
