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

namespace League\CommonMark\Node\Inline;

use League\CommonMark\Node\StringContainerInterface;

class AbstractStringContainer extends AbstractInline implements StringContainerInterface {
    /**
     * @var string
     */
    protected $literal = '';

    /**
     * @param array<string, mixed> $data
     * @param mixed                $contents
     */
    public function __construct($contents = '', array $data = []) {
        parent::__construct();

        $this->literal = $contents;
        $this->data->import($data);
    }

    public function getLiteral() {
        return $this->literal;
    }

    public function setLiteral($contents) {
        $this->literal = $contents;
    }
}
