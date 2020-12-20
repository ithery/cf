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

use PHPUnit\TextUI\XmlConfiguration\Filesystem\Directory;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Xml {
    /**
     * @var Directory
     */
    private $target;

    public function __construct(Directory $target) {
        $this->target = $target;
    }

    public function target() {
        return $this->target;
    }
}
