<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\CommonMark\Node\Block;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\StringContainerInterface;

class IndentedCode extends AbstractBlock implements StringContainerInterface {
    /**
     * @var string
     */
    protected $literal = '';

    public function getLiteral(): string {
        return $this->literal;
    }

    public function setLiteral($literal) {
        $this->literal = $literal;
    }
}
