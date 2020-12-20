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
final class TestSuiteCollectionIterator implements Countable, Iterator {
    /**
     * @var TestSuite[]
     */
    private $testSuites;

    /**
     * @var int
     */
    private $position;

    public function __construct(TestSuiteCollection $testSuites) {
        $this->testSuites = $testSuites->asArray();
    }

    public function count() {
        return iterator_count($this);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function valid() {
        return $this->position < count($this->testSuites);
    }

    public function key() {
        return $this->position;
    }

    public function current() {
        return $this->testSuites[$this->position];
    }

    public function next() {
        $this->position++;
    }
}
