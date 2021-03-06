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
final class InvalidMethodNameException extends \PHPUnit\Framework\Exception\Exception implements Exception {
    public function __construct($method) {
        parent::__construct(
            sprintf(
                'Cannot stub or mock method with invalid name "%s"',
                $method
            )
        );
    }
}
