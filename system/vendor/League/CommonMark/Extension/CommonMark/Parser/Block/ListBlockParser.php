<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\CommonMark\Parser\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

final class ListBlockParser extends AbstractBlockContinueParser {
    /**
     * @var ListBlock
     *
     * @psalm-readonly
     */
    private $block;

    /**
     * @var bool
     */
    private $hadBlankLine = false;

    /**
     * @var int
     */
    private $linesAfterBlank = 0;

    public function __construct(ListData $listData) {
        $this->block = new ListBlock($listData);
    }

    /**
     * @return ListBlock
     */
    public function getBlock() {
        return $this->block;
    }

    public function isContainer() {
        return true;
    }

    public function canContain(AbstractBlock $childBlock) {
        if (!$childBlock instanceof ListItem) {
            return false;
        }

        // Another list item is being added to this list block.
        // If the previous line was blank, that means this list
        // block is "loose" (not tight).
        if ($this->hadBlankLine && $this->linesAfterBlank === 1) {
            $this->block->setTight(false);
            $this->hadBlankLine = false;
        }

        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser) {
        if ($cursor->isBlank()) {
            $this->hadBlankLine = true;
            $this->linesAfterBlank = 0;
        } elseif ($this->hadBlankLine) {
            $this->linesAfterBlank++;
        }

        // List blocks themselves don't have any markers, only list items. So try to stay in the list.
        // If there is a block start other than list item, canContain makes sure that this list is closed.
        return BlockContinue::at($cursor);
    }
}
