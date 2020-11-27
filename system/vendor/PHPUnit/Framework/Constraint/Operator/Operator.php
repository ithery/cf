<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\Constraint\Operator;

use PHPUnit\Framework\Constraint\Cardinality\IsEqual;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract class Operator extends Constraint {

    /**
     * Returns the name of this operator.
     */
    abstract public function operator();

    /**
     * Returns this operator's precedence.
     *
     * @see https://www.php.net/manual/en/language.operators.precedence.php
     */
    abstract public function precedence();

    /**
     * Returns the number of operands.
     */
    abstract public function arity();

    /**
     * Validates $constraint argument.
     */
    protected function checkConstraint($constraint) {
        if (!$constraint instanceof Constraint) {
            return new IsEqual($constraint);
        }

        return $constraint;
    }

    /**
     * Returns true if the $constraint needs to be wrapped with braces.
     */
    protected function constraintNeedsParentheses(Constraint $constraint) {
        return $constraint instanceof self &&
                $constraint->arity() > 1 &&
                $this->precedence() <= $constraint->precedence();
    }

}
