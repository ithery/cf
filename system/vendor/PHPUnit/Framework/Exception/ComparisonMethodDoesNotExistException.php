<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Exception;

use const PHP_EOL;
use function sprintf;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ComparisonMethodDoesNotExistException extends Exception
{
    public function __construct($className, $methodName)
    {
        parent::__construct(
            sprintf(
                'Comparison method %s::%s() does not exist.',
                $className,
                $methodName
            ),
            0,
            null
        );
    }

    public function __toString()
    {
        return $this->getMessage() . PHP_EOL;
    }
}
