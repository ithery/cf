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

namespace League\CommonMark\Reference;

use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\LinkParserHelper;

final class ReferenceParser {
    // Looking for the start of a definition, i.e. `[`
    const START_DEFINITION = 0;

    // Looking for and parsing the label, i.e. `[foo]` within `[foo]`
    const LABEL = 1;

    // Parsing the destination, i.e. `/url` in `[foo]: /url`
    const DESTINATION = 2;

    // Looking for the start of a title, i.e. the first `"` in `[foo]: /url "title"`
    const START_TITLE = 3;

    // Parsing the content of the title, i.e. `title` in `[foo]: /url "title"`
    const TITLE = 4;

    // End state, no matter what kind of lines we add, they won't be references
    const PARAGRAPH = 5;

    /**
     * @var string
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $paragraph = '';

    /**
     * @var array<int, ReferenceInterface>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $references = [];

    /**
     * @var int
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $state = self::START_DEFINITION;

    /**
     * @var string|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $label;

    /**
     * @var string|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $destination;

    /**
     * @var string string
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $title = '';

    /**
     * @var string|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $titleDelimiter;

    /**
     * @var bool
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $referenceValid = false;

    public function getParagraphContent() {
        return $this->paragraph;
    }

    /**
     * @return ReferenceInterface[]
     */
    public function getReferences() {
        $this->finishReference();

        return $this->references;
    }

    public function hasReferences() {
        return $this->references !== [];
    }

    public function parse($line) {
        if ($this->paragraph !== '') {
            $this->paragraph .= "\n";
        }

        $this->paragraph .= $line;

        $cursor = new Cursor($line);
        while (!$cursor->isAtEnd()) {
            $result = false;
            switch ($this->state) {
                case self::PARAGRAPH:
                    // We're in a paragraph now. Link reference definitions can only appear at the beginning, so once
                    // we're in a paragraph, there's no going back.
                    return;
                case self::START_DEFINITION:
                    $result = $this->parseStartDefinition($cursor);
                    break;
                case self::LABEL:
                    $result = $this->parseLabel($cursor);
                    break;
                case self::DESTINATION:
                    $result = $this->parseDestination($cursor);
                    break;
                case self::START_TITLE:
                    $result = $this->parseStartTitle($cursor);
                    break;
                case self::TITLE:
                    $result = $this->parseTitle($cursor);
                    break;
                default:
                    // this should never happen
                    break;
            }

            if (!$result) {
                $this->state = self::PARAGRAPH;

                return;
            }
        }
    }

    private function parseStartDefinition(Cursor $cursor) {
        $cursor->advanceToNextNonSpaceOrTab();
        if ($cursor->isAtEnd() || $cursor->getCharacter() !== '[') {
            return false;
        }

        $this->state = self::LABEL;
        $this->label = '';

        $cursor->advance();
        if ($cursor->isAtEnd()) {
            $this->label .= "\n";
        }

        return true;
    }

    private function parseLabel(Cursor $cursor) {
        $cursor->advanceToNextNonSpaceOrTab();

        $partialLabel = LinkParserHelper::parsePartialLinkLabel($cursor);
        if ($partialLabel === null) {
            return false;
        }

        \assert($this->label !== null);
        $this->label .= $partialLabel;

        if ($cursor->isAtEnd()) {
            // label might continue on next line
            $this->label .= "\n";

            return true;
        }

        if ($cursor->getCharacter() !== ']') {
            return false;
        }

        $cursor->advance();

        // end of label
        if ($cursor->getCharacter() !== ':') {
            return false;
        }

        $cursor->advance();

        // spec: A link label can have at most 999 characters inside the square brackets
        if (\mb_strlen($this->label, 'utf-8') > 999) {
            return false;
        }

        // spec: A link label must contain at least one non-whitespace character
        if (\trim($this->label) === '') {
            return false;
        }

        $cursor->advanceToNextNonSpaceOrTab();

        $this->state = self::DESTINATION;

        return true;
    }

    private function parseDestination(Cursor $cursor) {
        $cursor->advanceToNextNonSpaceOrTab();

        $destination = LinkParserHelper::parseLinkDestination($cursor);
        if ($destination === null) {
            return false;
        }

        $this->destination = $destination;

        $advanced = $cursor->advanceToNextNonSpaceOrTab();
        if ($cursor->isAtEnd()) {
            // Destination was at end of line, so this is a valid reference for sure (and maybe a title).
            // If not at end of line, wait for title to be valid first.
            $this->referenceValid = true;
            $this->paragraph = '';
        } elseif ($advanced === 0) {
            // spec: The title must be separated from the link destination by whitespace
            return false;
        }

        $this->state = self::START_TITLE;

        return true;
    }

    private function parseStartTitle(Cursor $cursor) {
        $cursor->advanceToNextNonSpaceOrTab();
        if ($cursor->isAtEnd()) {
            $this->state = self::START_DEFINITION;

            return true;
        }

        $this->titleDelimiter = null;
        switch ($c = $cursor->getCharacter()) {
            case '"':
            case "'":
                $this->titleDelimiter = $c;
                break;
            case '(':
                $this->titleDelimiter = ')';
                break;
            default:
                // no title delimter found
                break;
        }

        if ($this->titleDelimiter !== null) {
            $this->state = self::TITLE;
            $cursor->advance();
            if ($cursor->isAtEnd()) {
                $this->title .= "\n";
            }
        } else {
            $this->finishReference();
            // There might be another reference instead, try that for the same character.
            $this->state = self::START_DEFINITION;
        }

        return true;
    }

    private function parseTitle(Cursor $cursor) {
        \assert($this->titleDelimiter !== null);
        $title = LinkParserHelper::parsePartialLinkTitle($cursor, $this->titleDelimiter);

        if ($title === null) {
            // Invalid title, stop
            return false;
        }

        // Did we find the end delimiter?
        $endDelimiterFound = false;
        if (\substr($title, -1) === $this->titleDelimiter) {
            $endDelimiterFound = true;
            // Chop it off
            $title = \substr($title, 0, -1);
        }

        $this->title .= $title;

        if (!$endDelimiterFound && $cursor->isAtEnd()) {
            // Title still going, continue on next line
            $this->title .= "\n";

            return true;
        }

        // We either hit the end delimiter or some extra whitespace
        $cursor->advanceToNextNonSpaceOrTab();
        if (!$cursor->isAtEnd()) {
            // spec: No further non-whitespace characters may occur on the line.
            return false;
        }

        $this->referenceValid = true;
        $this->finishReference();
        $this->paragraph = '';

        // See if there's another definition
        $this->state = self::START_DEFINITION;

        return true;
    }

    private function finishReference() {
        if (!$this->referenceValid) {
            return;
        }

        /** @psalm-suppress PossiblyNullArgument -- these can't possibly be null if we're in this state */
        $this->references[] = new Reference($this->label, $this->destination, $this->title);

        $this->label = null;
        $this->referenceValid = false;
        $this->destination = null;
        $this->title = '';
        $this->titleDelimiter = null;
    }
}
