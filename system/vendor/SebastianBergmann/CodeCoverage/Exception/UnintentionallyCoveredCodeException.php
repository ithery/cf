<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\CodeCoverage\Exception;

use RuntimeException;

final class UnintentionallyCoveredCodeException extends RuntimeException implements Exception {
    /**
     * @var array
     */
    private $unintentionallyCoveredUnits;

    public function __construct(array $unintentionallyCoveredUnits) {
        $this->unintentionallyCoveredUnits = $unintentionallyCoveredUnits;

        parent::__construct($this->toString());
    }

    public function getUnintentionallyCoveredUnits() {
        return $this->unintentionallyCoveredUnits;
    }

    private function toString() {
        $message = '';

        foreach ($this->unintentionallyCoveredUnits as $unit) {
            $message .= '- ' . $unit . "\n";
        }

        return $message;
    }
}
