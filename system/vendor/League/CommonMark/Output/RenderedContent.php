<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Output;

use League\CommonMark\Node\Block\Document;

class RenderedContent implements RenderedContentInterface {
    /**
     * @var Document
     *
     * @psalm-readonly
     */
    private $document;

    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $html;

    public function __construct(Document $document, $html) {
        $this->document = $document;
        $this->html = $html;
    }

    public function getDocument() {
        return $this->document;
    }

    public function getContent() {
        return $this->html;
    }

    /**
     * @psalm-mutation-free
     */
    public function __toString() {
        return $this->html;
    }
}
