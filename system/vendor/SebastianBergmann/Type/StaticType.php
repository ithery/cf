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

final class StaticType extends Type {
    private $className;

    private $allowsNull;

    public function __construct(TypeName $className, $allowsNull) {
        $this->className = $className;
        $this->allowsNull = $allowsNull;
    }

    public function isAssignable(Type $other) {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if (!$other instanceof ObjectType) {
            return false;
        }

        if (0 === strcasecmp($this->className->qualifiedName(), $other->className()->qualifiedName())) {
            return true;
        }

        if (is_subclass_of($other->className()->qualifiedName(), $this->className->qualifiedName(), true)) {
            return true;
        }

        return false;
    }

    public function name() {
        return 'static';
    }

    public function allowsNull() {
        return $this->allowsNull;
    }

    public function isStatic() {
        return true;
    }
}
