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
use function iterator_count;
use Countable;
use Iterator;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestDirectoryCollectionIterator implements Countable, Iterator {
    /**
     * @var TestDirectory[]
     */
    private $directories;

    /**
     * @var int
     */
    private $position;

    public function __construct(TestDirectoryCollection $directories) {
        $this->directories = $directories->asArray();
    }

    #[\ReturnTypeWillChange]
    public function count() {
        return iterator_count($this);
    }

    #[\ReturnTypeWillChange]
    public function rewind() {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function valid() {
        return $this->position < count($this->directories);
    }

    #[\ReturnTypeWillChange]
    public function key() {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function current() {
        return $this->directories[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function next() {
        $this->position++;
    }
}
