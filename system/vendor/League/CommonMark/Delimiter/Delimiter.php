<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter;

use League\CommonMark\Node\Inline\AbstractStringContainer;

final class Delimiter implements DelimiterInterface {
    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $char;

    /**
     * @var int
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $length;

    /**
     * @var int
     *
     * @psalm-readonly
     */
    private $originalLength;

    /**
     * @var AbstractStringContainer
     *
     * @psalm-readonly
     */
    private $inlineNode;

    /**
     * @var DelimiterInterface|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $previous;

    /**
     * @var DelimiterInterface|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $next;

    /**
     * @var bool
     *
     * @psalm-readonly
     */
    private $canOpen;

    /**
     * @var bool
     *
     * @psalm-readonly
     */
    private $canClose;

    /**
     * @var bool
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $active;

    /**
     * @var int|null
     *
     * @psalm-readonly
     */
    private $index;

    public function __construct($char, $numDelims, AbstractStringContainer $node, $canOpen, $canClose, $index = null) {
        $this->char = $char;
        $this->length = $numDelims;
        $this->originalLength = $numDelims;
        $this->inlineNode = $node;
        $this->canOpen = $canOpen;
        $this->canClose = $canClose;
        $this->active = true;
        $this->index = $index;
    }

    public function canClose() {
        return $this->canClose;
    }

    public function canOpen() {
        return $this->canOpen;
    }

    public function isActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function getChar() {
        return $this->char;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getNext() {
        return $this->next;
    }

    public function setNext(DelimiterInterface $next = null) {
        $this->next = $next;
    }

    public function getLength() {
        return $this->length;
    }

    public function setLength($length) {
        $this->length = $length;
    }

    public function getOriginalLength() {
        return $this->originalLength;
    }

    public function getInlineNode() {
        return $this->inlineNode;
    }

    public function getPrevious() {
        return $this->previous;
    }

    public function setPrevious(DelimiterInterface $previous = null) {
        $this->previous = $previous;
    }
}
