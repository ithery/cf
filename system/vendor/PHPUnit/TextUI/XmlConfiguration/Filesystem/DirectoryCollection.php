<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\Filesystem;

use function count;
use Countable;
use IteratorAggregate;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class DirectoryCollection implements Countable, IteratorAggregate {
    /**
     * @var Directory[]
     */
    private $directories;

    /**
     * @param Directory[] $directories
     */
    public static function fromArray(array $directories) {
        return new self(...$directories);
    }

    private function __construct(Directory ...$directories) {
        $this->directories = $directories;
    }

    /**
     * @return Directory[]
     */
    public function asArray() {
        return $this->directories;
    }

    public function count() {
        return count($this->directories);
    }

    public function getIterator() {
        return new DirectoryCollectionIterator($this);
    }

    public function isEmpty() {
        return $this->count() === 0;
    }
}
