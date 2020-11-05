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

use function is_array;
use function sprintf;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract class TraversableContains extends Constraint
{
    /**
     * @var mixed
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function toString()
    {
        return 'contains ' . $this->exporter()->export($this->value);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function failureDescription($other)
    {
        return sprintf(
            '%s %s',
            is_array($other) ? 'an array' : 'a traversable',
            $this->toString()
        );
    }

    protected function value()
    {
        return $this->value;
    }
}
