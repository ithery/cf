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
final class Text {
    /**
     * @var File
     */
    private $target;

    /**
     * @var bool
     */
    private $showUncoveredFiles;

    /**
     * @var bool
     */
    private $showOnlySummary;

    public function __construct(File $target, $showUncoveredFiles, $showOnlySummary) {
        $this->target = $target;
        $this->showUncoveredFiles = $showUncoveredFiles;
        $this->showOnlySummary = $showOnlySummary;
    }

    public function target() {
        return $this->target;
    }

    public function showUncoveredFiles() {
        return $this->showUncoveredFiles;
    }

    public function showOnlySummary() {
        return $this->showOnlySummary;
    }
}
