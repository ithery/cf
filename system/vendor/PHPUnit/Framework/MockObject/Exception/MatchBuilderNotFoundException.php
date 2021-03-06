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
final class MatchBuilderNotFoundException extends \PHPUnit\Framework\Exception\Exception implements Exception {
    public function __construct($id) {
        parent::__construct(
            sprintf(
                'No builder found for match builder identification <%s>',
                $id
            )
        );
    }
}
