<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\PHP;

use function count;
use Countable;
use IteratorAggregate;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class ConstantCollection implements Countable, IteratorAggregate {
    /**
     * @var Constant[]
     */
    private $constants;

    /**
     * @param Constant[] $constants
     */
    public static function fromArray(array $constants) {
        return new self(...$constants);
    }

    private function __construct(Constant ...$constants) {
        $this->constants = $constants;
    }

    /**
     * @return Constant[]
     */
    public function asArray() {
        return $this->constants;
    }

    public function count() {
        return count($this->constants);
    }

    public function getIterator() {
        return new ConstantCollectionIterator($this);
    }
}
