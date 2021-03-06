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

namespace League\CommonMark\Parser;

use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Reference\ReferenceMapInterface;

final class InlineParserContext {
    /**
     * @var AbstractBlock
     *
     * @psalm-readonly
     */
    private $container;

    /**
     * @var ReferenceMapInterface
     *
     * @psalm-readonly
     */
    private $referenceMap;

    /**
     * @var Cursor
     *
     * @psalm-readonly
     */
    private $cursor;

    /**
     * @var DelimiterStack
     *
     * @psalm-readonly
     */
    private $delimiterStack;

    /**
     * @var string[]
     * @psalm-var non-empty-array<string>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $matches;

    public function __construct(Cursor $contents, AbstractBlock $container, ReferenceMapInterface $referenceMap) {
        $this->referenceMap = $referenceMap;
        $this->container = $container;
        $this->cursor = $contents;
        $this->delimiterStack = new DelimiterStack();
    }

    public function getContainer() {
        return $this->container;
    }

    public function getReferenceMap() {
        return $this->referenceMap;
    }

    public function getCursor() {
        return $this->cursor;
    }

    public function getDelimiterStack() {
        return $this->delimiterStack;
    }

    /**
     * @return string The full text that matched the InlineParserMatch definition
     */
    public function getFullMatch() {
        return $this->matches[0];
    }

    /**
     * @return int The length of the full match (in characters, not bytes)
     */
    public function getFullMatchLength() {
        return \mb_strlen($this->matches[0]);
    }

    /**
     * @return string[] Similar to preg_match(), index 0 will contain the full match, and any other array elements will be captured sub-matches
     *
     * @psalm-return non-empty-array<string>
     */
    public function getMatches() {
        return $this->matches;
    }

    /**
     * @return string[]
     */
    public function getSubMatches() {
        return \array_slice($this->matches, 1);
    }

    /**
     * @param string[] $matches
     *
     * @psalm-param non-empty-array<string> $matches
     */
    public function withMatches(array $matches) {
        $ctx = clone $this;

        $ctx->matches = $matches;

        return $ctx;
    }
}
