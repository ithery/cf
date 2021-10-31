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
use League\CommonMark\Node\StringContainerInterface;

class FencedCode extends AbstractBlock implements StringContainerInterface {
    /**
     * @var string|null
     */
    protected $info;

    /**
     * @var string
     */
    protected $literal = '';

    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $char;

    /**
     * @var int
     */
    protected $offset;

    public function __construct($length, $char, $offset) {
        parent::__construct();

        $this->length = $length;
        $this->char = $char;
        $this->offset = $offset;
    }

    public function getInfo() {
        return $this->info;
    }

    /**
     * @return string[]
     */
    public function getInfoWords() {
        return \preg_split('/\s+/', $this->info ?: '') ?: [];
    }

    public function setInfo($info) {
        $this->info = $info;
    }

    public function getLiteral() {
        return $this->literal;
    }

    public function setLiteral($literal) {
        $this->literal = $literal;
    }

    public function getChar() {
        return $this->char;
    }

    public function setChar($char) {
        $this->char = $char;
    }

    public function getLength() {
        return $this->length;
    }

    public function setLength($length) {
        $this->length = $length;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
    }
}
