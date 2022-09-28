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

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\InlineParserEngineInterface;

/**
 * Base class for a block parser.
 *
 * Slightly more convenient to extend from vs. implementing the interface
 */
abstract class AbstractBlockContinueParser implements BlockContinueParserInterface {
    public function isContainer() {
        return false;
    }

    public function canHaveLazyContinuationLines() {
        return false;
    }

    public function canContain(AbstractBlock $childBlock) {
        return false;
    }

    public function addLine($line) {
    }

    public function closeBlock() {
    }

    public function parseInlines(InlineParserEngineInterface $inlineParser) {
    }
}
