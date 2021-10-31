<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report;

use PHPUnit\TextUI\XmlConfiguration\Filesystem\File;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Crap4j {
    /**
     * @var File
     */
    private $target;

    /**
     * @var int
     */
    private $threshold;

    public function __construct(File $target, $threshold) {
        $this->target = $target;
        $this->threshold = $threshold;
    }

    public function target() {
        return $this->target;
    }

    public function threshold() {
        return $this->threshold;
    }
}
