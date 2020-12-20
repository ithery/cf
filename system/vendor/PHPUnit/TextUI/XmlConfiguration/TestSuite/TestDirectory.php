<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\TestSuite;

use PHPUnit\Util\VersionComparisonOperator;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class TestDirectory {
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @var string
     */
    private $phpVersion;

    /**
     * @var VersionComparisonOperator
     */
    private $phpVersionOperator;

    public function __construct($path, $prefix, $suffix, $phpVersion, VersionComparisonOperator $phpVersionOperator) {
        $this->path = $path;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->phpVersion = $phpVersion;
        $this->phpVersionOperator = $phpVersionOperator;
    }

    public function path() {
        return $this->path;
    }

    public function prefix() {
        return $this->prefix;
    }

    public function suffix() {
        return $this->suffix;
    }

    public function phpVersion() {
        return $this->phpVersion;
    }

    public function phpVersionOperator() {
        return $this->phpVersionOperator;
    }
}
