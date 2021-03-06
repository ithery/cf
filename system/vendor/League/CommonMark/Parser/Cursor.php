<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser;

use League\CommonMark\Exception\UnexpectedEncodingException;

class Cursor {
    const INDENT_LEVEL = 4;

    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $line;

    /**
     * @var int
     *
     * @psalm-readonly
     */
    private $length;

    /**
     * @var int
     *
     * It's possible for this to be 1 char past the end, meaning we've parsed all chars and have
     * reached the end.  In this state, any character-returning method MUST return null.
     */
    private $currentPosition = 0;

    /**
     * @var int
     */
    private $column = 0;

    /**
     * @var int
     */
    private $indent = 0;

    /**
     * @var int
     */
    private $previousPosition = 0;

    /**
     * @var int|null
     */
    private $nextNonSpaceCache;

    /**
     * @var bool
     */
    private $partiallyConsumedTab = false;

    /**
     * @var bool
     *
     * @psalm-readonly
     */
    private $lineContainsTabs;

    /**
     * @var bool
     *
     * @psalm-readonly
     */
    private $isMultibyte;

    /**
     * @var array<int, string>
     */
    private $charCache = [];

    /**
     * @param string $line The line being parsed (ASCII or UTF-8)
     */
    public function __construct($line) {
        if (!\mb_check_encoding($line, 'UTF-8')) {
            throw new UnexpectedEncodingException('Unexpected encoding - UTF-8 or ASCII was expected');
        }

        $this->line = $line;
        $this->length = \mb_strlen($line, 'UTF-8') ?: 0;
        $this->isMultibyte = $this->length !== \strlen($line);
        $this->lineContainsTabs = \strpos($line, "\t") !== false;
    }

    /**
     * Returns the position of the next character which is not a space (or tab)
     */
    public function getNextNonSpacePosition() {
        if ($this->nextNonSpaceCache !== null) {
            return $this->nextNonSpaceCache;
        }

        $c = null;
        $i = $this->currentPosition;
        $cols = $this->column;

        while (($c = $this->getCharacter($i)) !== null) {
            if ($c === ' ') {
                $i++;
                $cols++;
            } elseif ($c === "\t") {
                $i++;
                $cols += 4 - ($cols % 4);
            } else {
                break;
            }
        }

        $nextNonSpace = $c === null ? $this->length : $i;
        $this->indent = $cols - $this->column;

        return $this->nextNonSpaceCache = $nextNonSpace;
    }

    /**
     * Returns the next character which isn't a space (or tab)
     */
    public function getNextNonSpaceCharacter() {
        return $this->getCharacter($this->getNextNonSpacePosition());
    }

    /**
     * Calculates the current indent (number of spaces after current position)
     */
    public function getIndent() {
        if ($this->nextNonSpaceCache === null) {
            $this->getNextNonSpacePosition();
        }

        return $this->indent;
    }

    /**
     * Whether the cursor is indented to INDENT_LEVEL
     */
    public function isIndented() {
        return $this->getIndent() >= self::INDENT_LEVEL;
    }

    public function getCharacter($index = null) {
        if ($index === null) {
            $index = $this->currentPosition;
        }

        // Index out-of-bounds, or we're at the end
        if ($index < 0 || $index >= $this->length) {
            return null;
        }

        if ($this->isMultibyte) {
            if (isset($this->charCache[$index])) {
                return $this->charCache[$index];
            }

            return $this->charCache[$index] = \mb_substr($this->line, $index, 1, 'UTF-8');
        }

        return $this->line[$index];
    }

    /**
     * Returns the next character (or null, if none) without advancing forwards
     *
     * @param mixed $offset
     */
    public function peek($offset = 1) {
        return $this->getCharacter($this->currentPosition + $offset);
    }

    /**
     * Whether the remainder is blank
     */
    public function isBlank() {
        return $this->nextNonSpaceCache === $this->length || $this->getNextNonSpacePosition() === $this->length;
    }

    /**
     * Move the cursor forwards
     */
    public function advance() {
        $this->advanceBy(1);
    }

    /**
     * Move the cursor forwards
     *
     * @param int  $characters       Number of characters to advance by
     * @param bool $advanceByColumns Whether to advance by columns instead of spaces
     */
    public function advanceBy($characters, $advanceByColumns = false) {
        if ($characters === 0) {
            $this->previousPosition = $this->currentPosition;

            return;
        }

        $this->previousPosition = $this->currentPosition;
        $this->nextNonSpaceCache = null;

        // Optimization to avoid tab handling logic if we have no tabs
        if (!$this->lineContainsTabs) {
            $this->advanceWithoutTabCharacters($characters);

            return;
        }

        $nextFewChars = $this->isMultibyte
            ? \mb_substr($this->line, $this->currentPosition, $characters, 'UTF-8')
            : \substr($this->line, $this->currentPosition, $characters);

        // Optimization to avoid tab handling logic if we have no tabs
        if (\strpos($nextFewChars, "\t") === false) {
            $this->advanceWithoutTabCharacters($characters);

            return;
        }

        if ($nextFewChars === '') {
            $this->previousPosition = $this->currentPosition;

            return;
        }

        if ($characters === 1) {
            $asArray = [$nextFewChars];
        } elseif ($this->isMultibyte) {
            /** @var string[] $asArray */
            $asArray = \preg_split('//u', $nextFewChars, -1, \PREG_SPLIT_NO_EMPTY);
        } else {
            $asArray = \str_split($nextFewChars);
        }

        foreach ($asArray as $relPos => $c) {
            if ($c === "\t") {
                $charsToTab = 4 - ($this->column % 4);
                if ($advanceByColumns) {
                    $this->partiallyConsumedTab = $charsToTab > $characters;
                    $charsToAdvance = $charsToTab > $characters ? $characters : $charsToTab;
                    $this->column += $charsToAdvance;
                    $this->currentPosition += $this->partiallyConsumedTab ? 0 : 1;
                    $characters -= $charsToAdvance;
                } else {
                    $this->partiallyConsumedTab = false;
                    $this->column += $charsToTab;
                    $this->currentPosition++;
                    $characters--;
                }
            } else {
                $this->partiallyConsumedTab = false;
                $this->currentPosition++;
                $this->column++;
                $characters--;
            }

            if ($characters <= 0) {
                break;
            }
        }
    }

    private function advanceWithoutTabCharacters($characters) {
        $length = \min($characters, $this->length - $this->currentPosition);
        $this->partiallyConsumedTab = false;
        $this->currentPosition += $length;
        $this->column += $length;

        return;
    }

    /**
     * Advances the cursor by a single space or tab, if present
     */
    public function advanceBySpaceOrTab() {
        $character = $this->getCharacter();

        if ($character === ' ' || $character === "\t") {
            $this->advanceBy(1, true);

            return true;
        }

        return false;
    }

    /**
     * Parse zero or more space/tab characters
     *
     * @return int Number of positions moved
     */
    public function advanceToNextNonSpaceOrTab() {
        $newPosition = $this->getNextNonSpacePosition();
        $this->advanceBy($newPosition - $this->currentPosition);
        $this->partiallyConsumedTab = false;

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * Parse zero or more space characters, including at most one newline.
     *
     * Tab characters are not parsed with this function.
     *
     * @return int Number of positions moved
     */
    public function advanceToNextNonSpaceOrNewline() {
        $remainder = $this->getRemainder();

        // Optimization: Avoid the regex if we know there are no spaces or newlines
        if ($remainder === '' || ($remainder[0] !== ' ' && $remainder[0] !== "\n")) {
            $this->previousPosition = $this->currentPosition;

            return 0;
        }

        $matches = [];
        \preg_match('/^ *(?:\n *)?/', $remainder, $matches, \PREG_OFFSET_CAPTURE);

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        $increment = $matches[0][1] + \strlen($matches[0][0]);

        $this->advanceBy($increment);

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * Move the position to the very end of the line
     *
     * @return int The number of characters moved
     */
    public function advanceToEnd() {
        $this->previousPosition = $this->currentPosition;
        $this->nextNonSpaceCache = null;

        $this->currentPosition = $this->length;

        return $this->currentPosition - $this->previousPosition;
    }

    public function getRemainder() {
        if ($this->currentPosition >= $this->length) {
            return '';
        }

        $prefix = '';
        $position = $this->currentPosition;
        if ($this->partiallyConsumedTab) {
            $position++;
            $charsToTab = 4 - ($this->column % 4);
            $prefix = \str_repeat(' ', $charsToTab);
        }

        $subString = $this->isMultibyte
            ? \mb_substr($this->line, $position, null, 'UTF-8')
            : \substr($this->line, $position);

        return $prefix . $subString;
    }

    public function getLine() {
        return $this->line;
    }

    public function isAtEnd() {
        return $this->currentPosition >= $this->length;
    }

    /**
     * Try to match a regular expression
     *
     * Returns the matching text and advances to the end of that match
     *
     * @param mixed $regex
     */
    public function match($regex) {
        $subject = $this->getRemainder();

        if (!\preg_match($regex, $subject, $matches, \PREG_OFFSET_CAPTURE)) {
            return null;
        }

        // $matches[0][0] contains the matched text
        // $matches[0][1] contains the index of that match

        if ($this->isMultibyte) {
            // PREG_OFFSET_CAPTURE always returns the byte offset, not the char offset, which is annoying
            $offset = \mb_strlen(\substr($subject, 0, $matches[0][1]), 'UTF-8');
            $matchLength = \mb_strlen($matches[0][0], 'UTF-8');
        } else {
            $offset = $matches[0][1];
            $matchLength = \strlen($matches[0][0]);
        }

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        $this->advanceBy($offset + $matchLength);

        return $matches[0][0];
    }

    /**
     * Encapsulates the current state of this cursor in case you need to rollback later.
     *
     * WARNING: Do not parse or use the return value for ANYTHING except for
     * passing it back into restoreState(), as the number of values and their
     * contents may change in any future release without warning.
     */
    public function saveState() {
        return new CursorState([
            $this->currentPosition,
            $this->previousPosition,
            $this->nextNonSpaceCache,
            $this->indent,
            $this->column,
            $this->partiallyConsumedTab,
        ]);
    }

    /**
     * Restore the cursor to a previous state.
     *
     * Pass in the value previously obtained by calling saveState().
     */
    public function restoreState(CursorState $state) {
        list(
            $this->currentPosition,
            $this->previousPosition,
            $this->nextNonSpaceCache,
            $this->indent,
            $this->column,
            $this->partiallyConsumedTab,
         ) = $state->toArray();
    }

    public function getPosition() {
        return $this->currentPosition;
    }

    public function getPreviousText() {
        return \mb_substr($this->line, $this->previousPosition, $this->currentPosition - $this->previousPosition, 'UTF-8');
    }

    public function getSubstring($start, $length = null) {
        if ($this->isMultibyte) {
            return \mb_substr($this->line, $start, $length, 'UTF-8');
        }

        if ($length !== null) {
            return \substr($this->line, $start, $length);
        }

        return \substr($this->line, $start);
    }

    public function getColumn() {
        return $this->column;
    }
}
