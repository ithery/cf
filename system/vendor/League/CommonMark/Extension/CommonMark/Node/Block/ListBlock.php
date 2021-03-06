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

namespace League\CommonMark\Extension\CommonMark\Node\Block;

use League\CommonMark\Node\Block\AbstractBlock;

class ListBlock extends AbstractBlock {
    const TYPE_BULLET = 'bullet';

    const TYPE_ORDERED = 'ordered';

    /**
     * @var bool
     */
    protected $tight = false;

    /**
     * @var ListData
     *
     * @psalm-readonly
     */
    protected $listData;

    public function __construct(ListData $listData) {
        parent::__construct();

        $this->listData = $listData;
    }

    /**
     * @return ListData
     */
    public function getListData() {
        return $this->listData;
    }

    public function isTight() {
        return $this->tight;
    }

    public function setTight($tight) {
        $this->tight = $tight;
    }
}
