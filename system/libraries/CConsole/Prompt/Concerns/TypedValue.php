<?php

trait CConsole_Prompt_Concerns_TypedValue {
    /**
     * The value that has been typed.
     *
     * @var string
     */
    protected $typedValue = '';

    /**
     * The position of the virtual cursor.
     *
     * @var int
     */
    protected $cursorPosition = 0;

    /**
     * Track the value as the user types.
     *
     * @param string        $default
     * @param bool          $submit
     * @param null|callable $ignore
     * @param bool          $allowNewLine
     *
     * @return void
     */
    protected function trackTypedValue($default = '', $submit = true, $ignore = null, $allowNewLine = false) {
        $this->typedValue = $default;

        if (strlen($this->typedValue) > 0) {
            $this->cursorPosition = mb_strlen($this->typedValue);
        }

        $this->on('key', function ($key) use ($submit, $ignore, $allowNewLine) {
            if ($key !== ''
                && ($key[0] === "\e" || in_array($key, [CConsole_Prompt_Key::CTRL_B, CConsole_Prompt_Key::CTRL_F, CConsole_Prompt_Key::CTRL_A, CConsole_Prompt_Key::CTRL_E]))
            ) {
                if ($ignore !== null && $ignore($key)) {
                    return;
                }

                if (in_array($key, [CConsole_Prompt_Key::LEFT, CConsole_Prompt_Key::LEFT_ARROW, CConsole_Prompt_Key::CTRL_B])) {
                    $this->cursorPosition = max(0, $this->cursorPosition - 1);
                } elseif (in_array($key, [CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW, CConsole_Prompt_Key::CTRL_F])) {
                    $this->cursorPosition = min(mb_strlen($this->typedValue), $this->cursorPosition + 1);
                } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::CTRL_A], $key) !== null) {
                    $this->cursorPosition = 0;
                } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_E], $key) !== null) {
                    $this->cursorPosition = mb_strlen($this->typedValue);
                } elseif ($key === CConsole_Prompt_Key::DELETE) {
                    $this->typedValue = mb_substr($this->typedValue, 0, $this->cursorPosition) . mb_substr($this->typedValue, $this->cursorPosition + 1);
                } elseif ($key === CConsole_Prompt_Key::OPTION_BACKSPACE) {
                    $this->deleteWordBackward();
                }

                return;
            }

            // Keys may be buffered.
            foreach (mb_str_split($key) as $key) {
                if ($ignore !== null && $ignore($key)) {
                    return;
                }

                if ($key === CConsole_Prompt_Key::ENTER) {
                    if ($submit) {
                        $this->submit();

                        return;
                    }

                    if ($allowNewLine) {
                        $this->typedValue = mb_substr($this->typedValue, 0, $this->cursorPosition) . PHP_EOL . mb_substr($this->typedValue, $this->cursorPosition);
                        $this->cursorPosition++;
                    }
                } elseif ($key === CConsole_Prompt_Key::BACKSPACE || $key === CConsole_Prompt_Key::CTRL_H) {
                    if ($this->cursorPosition === 0) {
                        return;
                    }

                    $this->typedValue = mb_substr($this->typedValue, 0, $this->cursorPosition - 1) . mb_substr($this->typedValue, $this->cursorPosition);
                    $this->cursorPosition--;
                } elseif (mb_ord($key) >= 32) {
                    $this->typedValue = mb_substr($this->typedValue, 0, $this->cursorPosition) . $key . mb_substr($this->typedValue, $this->cursorPosition);
                    $this->cursorPosition++;
                }
            }
        });
    }

    /**
     * Get the value of the prompt.
     *
     * @return string
     */
    public function value() {
        return $this->typedValue;
    }

    /**
     * Add a virtual cursor to the value and truncate if necessary.
     *
     * @param string   $value
     * @param int      $cursorPosition
     * @param null|int $maxWidth
     *
     * @return string
     */
    protected function addCursor($value, $cursorPosition, $maxWidth = null) {
        $before = mb_substr($value, 0, $cursorPosition);
        $current = mb_substr($value, $cursorPosition, 1);
        $after = mb_substr($value, $cursorPosition + 1);

        $cursor = mb_strlen($current) && $current !== PHP_EOL ? $current : ' ';

        $spaceBefore = $maxWidth < 0 || $maxWidth === null ? mb_strwidth($before) : $maxWidth - mb_strwidth($cursor) - (mb_strwidth($after) > 0 ? 1 : 0);
        if (mb_strwidth($before) > $spaceBefore) {
            $truncatedBefore = $this->trimWidthBackwards($before, 0, $spaceBefore - 1);
            $wasTruncatedBefore = true;
        } else {
            $truncatedBefore = $before;
            $wasTruncatedBefore = false;
        }

        $spaceAfter = $maxWidth < 0 || $maxWidth === null ? mb_strwidth($after) : $maxWidth - ($wasTruncatedBefore ? 1 : 0) - mb_strwidth($truncatedBefore) - mb_strwidth($cursor);
        if (mb_strwidth($after) > $spaceAfter) {
            $truncatedAfter = mb_strimwidth($after, 0, $spaceAfter - 1);
            $wasTruncatedAfter = true;
        } else {
            $truncatedAfter = $after;
            $wasTruncatedAfter = false;
        }

        return ($wasTruncatedBefore ? $this->dim('…') : '')
            . $truncatedBefore
            . $this->inverse($cursor)
            . ($current === PHP_EOL ? PHP_EOL : '')
            . $truncatedAfter
            . ($wasTruncatedAfter ? $this->dim('…') : '');
    }

    /**
     * Get a truncated string with the specified width from the end.
     *
     * @param string $string
     * @param int    $start
     * @param int    $width
     *
     * @return string
     */
    private function trimWidthBackwards($string, $start, $width) {
        $reversed = implode('', array_reverse(mb_str_split($string, 1)));

        $trimmed = mb_strimwidth($reversed, $start, $width);

        return implode('', array_reverse(mb_str_split($trimmed, 1)));
    }

    /**
     * Delete from the start of the current word (before cursor) through the cursor.
     *
     * @return void
     */
    protected function deleteWordBackward() {
        if ($this->cursorPosition === 0) {
            return;
        }

        $start = $this->findWordStartBeforeCursor();
        $this->typedValue = mb_substr($this->typedValue, 0, $start) . mb_substr($this->typedValue, $this->cursorPosition);
        $this->cursorPosition = $start;
    }

    /**
     * Character offset of the word boundary immediately before the cursor (Intl + punctuation).
     * Punctuation (e.g. . - _) is treated as a word boundary so "word.word" deletes in two steps.
     *
     * @return int
     */
    protected function findWordStartBeforeCursor() {
        $before = mb_substr($this->typedValue, 0, $this->cursorPosition);

        if ($before === '') {
            return 0;
        }

        $regexStart = $this->findLastWordStartByLettersAndNumbers($before);

        if (extension_loaded('intl')) {
            $iterator = IntlBreakIterator::createWordInstance('');
            $iterator->setText($before);
            $endByte = strlen($before);
            $wordStartByte = $iterator->preceding($endByte);

            if ($wordStartByte === IntlBreakIterator::DONE) {
                return $regexStart;
            }

            $intlStart = mb_strlen(substr($before, 0, $wordStartByte), 'UTF-8');

            return max($intlStart, $regexStart);
        }

        return $regexStart;
    }

    /**
     * Start (character offset) of the last run of letters/numbers in string (punctuation breaks words).
     *
     * @param string $before
     *
     * @return int
     */
    protected function findLastWordStartByLettersAndNumbers($before) {
        if (preg_match_all('/((?:\p{L}\p{M}*|\p{N})+)/u', $before, $m, PREG_OFFSET_CAPTURE) && $m[1] !== []) {
            $last = end($m[1]);

            return mb_strlen(substr($before, 0, $last[1]), 'UTF-8');
        }

        return 0;
    }
}
