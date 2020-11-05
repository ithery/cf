<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint\Traversable;

use SplObjectStorage;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class TraversableContainsIdentical extends TraversableContains
{
    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other)
    {
        if ($other instanceof SplObjectStorage) {
            return $other->contains($this->value());
        }

        foreach ($other as $element) {
            if ($this->value() === $element) {
                return true;
            }
        }

        return false;
    }
}
