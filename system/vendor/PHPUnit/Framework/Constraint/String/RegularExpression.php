<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint\String;

use function preg_match;
use function sprintf;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
class RegularExpression extends Constraint
{
    /**
     * @var string
     */
    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString()
    {
        return sprintf(
            'matches PCRE pattern "%s"',
            $this->pattern
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other)
    {
        return preg_match($this->pattern, $other) > 0;
    }
}
