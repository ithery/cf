<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\Constraint\Exception;

use Throwable;
use function sprintf;
use function get_class;
use PHPUnit\Util\Filter;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class Exception extends Constraint {
    /**
     * @var string
     */
    private $className;

    public function __construct($className) {
        $this->className = $className;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString() {
        return sprintf(
            'exception of type "%s"',
            $this->className
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other) {
        return $other instanceof $this->className;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     */
    protected function failureDescription($other) {
        if ($other !== null) {
            $message = '';

            if ($other instanceof \Exception) {
                $message = '. Message was: "' . $other->getMessage() . '" at'
                    . "\n" . Filter::getFilteredStacktrace($other);
            }

            return sprintf(
                'exception of type "%s" matches expected exception "%s"%s',
                get_class($other),
                $this->className,
                $message
            );
        }

        return sprintf(
            'exception of type "%s" is thrown',
            $this->className
        );
    }
}
