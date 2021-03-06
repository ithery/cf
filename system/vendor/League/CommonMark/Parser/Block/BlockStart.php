<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser\Block;

use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\CursorState;

/**
 * Result object for starting parsing of a block; see static methods for constructors
 */
final class BlockStart {
    /**
     * @var BlockContinueParserInterface[]
     *
     * @psalm-readonly
     */
    private $blockParsers;

    /**
     * @var CursorState|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $cursorState = null;

    /**
     * @var bool
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $replaceActiveBlockParser = false;

    private function __construct(BlockContinueParserInterface ...$blockParsers) {
        $this->blockParsers = $blockParsers;
    }

    /**
     * @return BlockContinueParserInterface[]
     */
    public function getBlockParsers() {
        return $this->blockParsers;
    }

    public function getCursorState() {
        return $this->cursorState;
    }

    public function isReplaceActiveBlockParser() {
        return $this->replaceActiveBlockParser;
    }

    /**
     * Signal that we want to parse at the given cursor position
     *
     * @return $this
     */
    public function at(Cursor $cursor) {
        $this->cursorState = $cursor->saveState();

        return $this;
    }

    /**
     * Signal that we want to replace the active block parser with this one
     *
     * @return $this
     */
    public function replaceActiveBlockParser() {
        $this->replaceActiveBlockParser = true;

        return $this;
    }

    /**
     * Signal that we cannot parse whatever is here
     *
     * @return null
     */
    public static function none() {
        return null;
    }

    /**
     * Signal that we'd like to register the given parser(s) so they can parse the current block
     */
    public static function of(BlockContinueParserInterface ...$blockParsers) {
        return new self(...$blockParsers);
    }
}
