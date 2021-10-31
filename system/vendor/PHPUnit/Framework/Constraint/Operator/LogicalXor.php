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

use function array_reduce;
use function array_shift;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class LogicalXor extends BinaryOperator {

    /**
     * Returns the name of this operator.
     */
    public function operator() {
        return 'xor';
    }

    /**
     * Returns this operator's precedence.
     *
     * @see https://www.php.net/manual/en/language.operators.precedence.php.
     */
    public function precedence() {
        return 23;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     */
    public function matches($other) {
        $constraints = $this->constraints();

        $initial = array_shift($constraints);

        if ($initial === null) {
            return false;
        }

        return array_reduce(
                $constraints, static function ($matches, Constraint $constraint) use ($other) {
            return $matches xor $constraint->evaluate($other, '', true);
        }, $initial->evaluate($other, '', true)
        );
    }

}
