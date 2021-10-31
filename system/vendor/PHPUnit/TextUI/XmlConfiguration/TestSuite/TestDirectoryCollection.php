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

use function count;
use Countable;
use IteratorAggregate;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class TestDirectoryCollection implements Countable, IteratorAggregate {
    /**
     * @var TestDirectory[]
     */
    private $directories;

    /**
     * @param TestDirectory[] $directories
     */
    public static function fromArray(array $directories) {
        return new self(...$directories);
    }

    private function __construct(TestDirectory ...$directories) {
        $this->directories = $directories;
    }

    /**
     * @return TestDirectory[]
     */
    public function asArray() {
        return $this->directories;
    }

    public function count() {
        return count($this->directories);
    }

    public function getIterator() {
        return new TestDirectoryCollectionIterator($this);
    }

    public function isEmpty() {
        return $this->count() === 0;
    }
}
