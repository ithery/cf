<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\CodeCoverage\Driver;

use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception\Exception;

final class Xdebug2NotEnabledException extends RuntimeException implements Exception {
    public function __construct() {
        parent::__construct('xdebug.coverage_enable=On has to be set in php.ini');
    }
}
