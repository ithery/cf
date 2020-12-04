<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use function sprintf;
use function strpos;
use Throwable;
use PHPUnit\Framework\Constraint\Constraint;
/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class ExceptionMessage extends Constraint
{
    /**
     * @var string
     */
    private $expectedMessage;

    public function __construct($expected)
    {
        $this->expectedMessage = $expected;
    }

    public function toString()
    {
        if ($this->expectedMessage === '') {
            return 'exception message is empty';
        }

        return 'exception message contains ';
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param Throwable $other
     */
    protected function matches($other)
    {
        if ($this->expectedMessage === '') {
            return $other->getMessage() === '';
        }

        return strpos((string) $other->getMessage(), $this->expectedMessage) !== false;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     */
    protected function failureDescription($other)
    {
        if ($this->expectedMessage === '') {
            return sprintf(
                "exception message is empty but is '%s'",
                $other->getMessage()
            );
        }

        return sprintf(
            "exception message '%s' contains '%s'",
            $other->getMessage(),
            $this->expectedMessage
        );
    }
}
