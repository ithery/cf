<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\MockObject\Exception;

use function sprintf;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ClassIsFinalException extends \PHPUnit\Framework\Exception\Exception implements Exception {
    public function __construct($className) {
        parent::__construct(
            sprintf(
                'Class "%s" is declared "final" and cannot be mocked',
                $className
            )
        );
    }
}
