<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Event;

use League\CommonMark\Input\MarkdownInputInterface;
use League\CommonMark\Node\Block\Document;

/**
 * Event dispatched when the document is about to be parsed
 */
final class DocumentPreParsedEvent extends AbstractEvent {
    /**
     * @var Document
     *
     * @psalm-readonly
     */
    private $document;

    /**
     * @var MarkdownInputInterface
     */
    private $markdown;

    public function __construct(Document $document, MarkdownInputInterface $markdown) {
        $this->document = $document;
        $this->markdown = $markdown;
    }

    public function getDocument() {
        return $this->document;
    }

    public function getMarkdown() {
        return $this->markdown;
    }

    public function replaceMarkdown(MarkdownInputInterface $markdownInput) {
        $this->markdown = $markdownInput;
    }
}
