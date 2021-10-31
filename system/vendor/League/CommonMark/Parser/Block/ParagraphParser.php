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
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserEngineInterface;
use League\CommonMark\Reference\ReferenceInterface;
use League\CommonMark\Reference\ReferenceParser;

final class ParagraphParser extends AbstractBlockContinueParser {
    /**
     * @var Paragraph
     *
     * @psalm-readonly
     */
    private $block;

    /**
     * @var ReferenceParser
     *
     * @psalm-readonly
     */
    private $referenceParser;

    public function __construct() {
        $this->block = new Paragraph();
        $this->referenceParser = new ReferenceParser();
    }

    public function canHaveLazyContinuationLines() {
        return true;
    }

    /**
     * @return Paragraph
     */
    public function getBlock() {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser) {
        if ($cursor->isBlank()) {
            return BlockContinue::none();
        }

        return BlockContinue::at($cursor);
    }

    public function addLine($line) {
        $this->referenceParser->parse($line);
    }

    public function closeBlock() {
        if ($this->referenceParser->hasReferences() && $this->referenceParser->getParagraphContent() === '') {
            $this->block->detach();
        }
    }

    public function parseInlines(InlineParserEngineInterface $inlineParser) {
        $content = $this->getContentString();
        if ($content !== '') {
            $inlineParser->parse($content, $this->block);
        }
    }

    public function getContentString() {
        return $this->referenceParser->getParagraphContent();
    }

    /**
     * @return ReferenceInterface[]
     */
    public function getReferences() {
        return $this->referenceParser->getReferences();
    }
}
