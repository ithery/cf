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

namespace League\CommonMark\Node\Block;

use League\CommonMark\Parser\Cursor;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Reference\ReferenceMapInterface;

class Document extends AbstractBlock {
    /**
     * @var ReferenceMapInterface
     *
     * @psalm-readonly
     */
    protected $referenceMap;

    public function __construct(ReferenceMapInterface $referenceMap = null) {
        parent::__construct();

        $this->setStartLine(1);

        $this->referenceMap = $referenceMap ?: new ReferenceMap();
    }

    /**
     * @return ReferenceMapInterface
     */
    public function getReferenceMap() {
        return $this->referenceMap;
    }

    public function canContain(AbstractBlock $block) {
        return true;
    }

    /**
     * @return bool
     */
    public function isCode() {
        return false;
    }

    public function matchesNextLine(Cursor $cursor) {
        return true;
    }
}
