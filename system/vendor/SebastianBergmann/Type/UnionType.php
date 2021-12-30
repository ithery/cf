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

use function sort;
use function count;
use function implode;

final class UnionType extends Type {
    /**
     * @psalm-var list<Type>
     */
    private array $types;

    /**
     * @throws RuntimeException
     */
    public function __construct(Type ...$types) {
        $this->ensureMinimumOfTwoTypes(...$types);
        $this->ensureOnlyValidTypes(...$types);

        $this->types = $types;
    }

    public function isAssignable(Type $other) {
        foreach ($this->types as $type) {
            if ($type->isAssignable($other)) {
                return true;
            }
        }

        return false;
    }

    public function asString() {
        return $this->name();
    }

    public function name() {
        $types = [];

        foreach ($this->types as $type) {
            $types[] = $type->name();
        }

        sort($types);

        return implode('|', $types);
    }

    public function allowsNull() {
        foreach ($this->types as $type) {
            if ($type instanceof NullType) {
                return true;
            }
        }

        return false;
    }

    public function isUnion() {
        return true;
    }

    /**
     * @throws RuntimeException
     */
    private function ensureMinimumOfTwoTypes(Type ...$types) {
        if (count($types) < 2) {
            throw new RuntimeException(
                'A union type must be composed of at least two types'
            );
        }
    }

    /**
     * @throws RuntimeException
     */
    private function ensureOnlyValidTypes(Type ...$types) {
        foreach ($types as $type) {
            if ($type instanceof UnknownType) {
                throw new RuntimeException(
                    'A union type must not be composed of an unknown type'
                );
            }

            if ($type instanceof VoidType) {
                throw new RuntimeException(
                    'A union type must not be composed of a void type'
                );
            }
        }
    }
}
