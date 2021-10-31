<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Node;

use function count;
use RecursiveIterator;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Iterator implements RecursiveIterator
{
    /**
     * @var int
     */
    private $position;

    /**
     * @var AbstractNode[]
     */
    private $nodes;

    public function __construct(Directory $node)
    {
        $this->nodes = $node->children();
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Checks if there is a current element after calls to rewind() or next().
     */
    public function valid()
    {
        return $this->position < count($this->nodes);
    }

    /**
     * Returns the key of the current element.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Returns the current element.
     */
    public function current()
    {
        return $this->valid() ? $this->nodes[$this->position] : null;
    }

    /**
     * Moves forward to next element.
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Returns the sub iterator for the current element.
     *
     * @return Iterator
     */
    public function getChildren()
    {
        return new self($this->nodes[$this->position]);
    }

    /**
     * Checks whether the current element has children.
     */
    public function hasChildren()
    {
        return $this->nodes[$this->position] instanceof Directory;
    }
}
