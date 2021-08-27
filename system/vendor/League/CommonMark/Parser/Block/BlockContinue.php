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
 * Result object for continuing parsing of a block; see static methods for constructors.
 *
 * @psalm-immutable
 */
final class BlockContinue {
    /**
     * @var CursorState|null
     *
     * @psalm-readonly
     */
    private $cursorState;

    /**
     * @var bool
     *
     * @psalm-readonly
     */
    private $finalize;

    private function __construct(CursorState $cursorState = null, $finalize = false) {
        $this->cursorState = $cursorState;
        $this->finalize = $finalize;
    }

    public function getCursorState() {
        return $this->cursorState;
    }

    public function isFinalize() {
        return $this->finalize;
    }

    /**
     * Signal that we cannot continue here
     *
     * @return null
     */
    public static function none() {
        return null;
    }

    /**
     * Signal that we're continuing at the given position
     */
    public static function at(Cursor $cursor) {
        return new self($cursor->saveState(), false);
    }

    /**
     * Signal that we want to finalize and close the block
     */
    public static function finished() {
        return new self(null, true);
    }
}
