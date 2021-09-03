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

use PHPUnit\TextUI\XmlConfiguration\Filesystem\FileCollection;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class TestSuite {
    /**
     * @var string
     */
    private $name;

    /**
     * @var TestDirectoryCollection
     */
    private $directories;

    /**
     * @var TestFileCollection
     */
    private $files;

    /**
     * @var FileCollection
     */
    private $exclude;

    public function __construct($name, TestDirectoryCollection $directories, TestFileCollection $files, FileCollection $exclude) {
        $this->name = $name;
        $this->directories = $directories;
        $this->files = $files;
        $this->exclude = $exclude;
    }

    public function name() {
        return $this->name;
    }

    public function directories() {
        return $this->directories;
    }

    public function files() {
        return $this->files;
    }

    public function exclude() {
        return $this->exclude;
    }
}
