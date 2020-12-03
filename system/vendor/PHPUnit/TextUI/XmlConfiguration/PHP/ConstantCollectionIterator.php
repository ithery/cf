<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use function count;
use function iterator_count;
use Countable;
use Iterator;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ConstantCollectionIterator implements Countable, Iterator
{
    /**
     * @var Constant[]
     */
    private $constants;

    /**
     * @var int
     */
    private $position;

    public function __construct(ConstantCollection $constants)
    {
        $this->constants = $constants->asArray();
    }

    public function count()
    {
        return iterator_count($this);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return $this->position < count($this->constants);
    }

    public function key()
    {
        return $this->position;
    }

    public function current()
    {
        return $this->constants[$this->position];
    }

    public function next()
    {
        $this->position++;
    }
}
