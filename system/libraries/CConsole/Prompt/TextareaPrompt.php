<?php

class CConsole_Prompt_TextareaPrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_Scrolling;
    use CConsole_Prompt_Concerns_TypedValue;
    use CConsole_Prompt_Themes_Default_Concerns_InteractsWithStrings;

    /**
     * @var int
     */
    protected $minWidth = 0;

    /**
     * The width of the textarea.
     *
     * @var int
     */
    public $width = 60;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $placeholder;

    /**
     * @var string
     */
    public $default;

    /**
     * @var string
     */
    public $hint;

    /**
     * Create a new TextareaPrompt instance.
     *
     * @param string       $label
     * @param string       $placeholder
     * @param string       $default
     * @param bool|string  $required
     * @param mixed        $validate
     * @param string       $hint
     * @param int          $rows
     * @param null|Closure $transform
     */
    public function __construct($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $rows = 5, $transform = null) {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->default = $default;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;

        $this->scroll = $rows;

        $this->initializeScrolling();

        $this->trackTypedValue($default, false, null, true);

        $this->on('key', function ($key) {
            if ($key[0] === "\e") {
                if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::CTRL_P])) {
                    $this->handleUpKey();
                } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::CTRL_N])) {
                    $this->handleDownKey();
                }

                return;
            }

            // Keys may be buffered.
            foreach (mb_str_split($key) as $key) {
                if ($key === CConsole_Prompt_Key::CTRL_D) {
                    $this->submit();

                    return;
                }
            }
        });
    }

    /**
     * Get the formatted value with a virtual cursor.
     *
     * @return string
     */
    public function valueWithCursor() {
        if ($this->value() === '') {
            return $this->wrappedPlaceholderWithCursor();
        }

        return $this->addCursor($this->wrappedValue(), $this->cursorPosition + $this->cursorOffset(), -1);
    }

    /**
     * The word-wrapped version of the typed value.
     *
     * @return string
     */
    public function wrappedValue() {
        return $this->mbWordwrap($this->value(), $this->width, PHP_EOL, true);
    }

    /**
     * The formatted lines.
     *
     * @return array<int, string>
     */
    public function lines() {
        return explode(PHP_EOL, $this->wrappedValue());
    }

    /**
     * The currently visible lines.
     *
     * @return array<int, string>
     */
    public function visible() {
        $this->adjustVisibleWindow();

        $withCursor = $this->valueWithCursor();

        return array_slice(explode(PHP_EOL, $withCursor), $this->firstVisible, $this->scroll, true);
    }

    /**
     * Handle the up key press.
     *
     * @return void
     */
    protected function handleUpKey() {
        if ($this->cursorPosition === 0) {
            return;
        }

        $lines = $this->lines();

        // Line length + 1 for the newline character
        $lineLengths = array_map(function ($line, $index) use ($lines) {
            return mb_strlen($line) + ($index === count($lines) - 1 ? 0 : 1);
        }, $lines, range(0, count($lines) - 1));

        $currentLineIndex = $this->currentLineIndex();

        if ($currentLineIndex === 0) {
            // They're already at the first line, jump them to the first position
            $this->cursorPosition = 0;

            return;
        }

        $currentLines = array_slice($lineLengths, 0, $currentLineIndex + 1);

        $currentColumn = CConsole_Prompt_Support_Utils::last($currentLines) - (array_sum($currentLines) - $this->cursorPosition);

        $destinationLineLength = (isset($lineLengths[$currentLineIndex - 1]) ? $lineLengths[$currentLineIndex - 1] : $currentLines[0]) - 1;

        $newColumn = min($destinationLineLength, $currentColumn);

        $fullLines = array_slice($currentLines, 0, -2);

        $this->cursorPosition = array_sum($fullLines) + $newColumn;
    }

    /**
     * Handle the down key press.
     *
     * @return void
     */
    protected function handleDownKey() {
        $lines = $this->lines();

        // Line length + 1 for the newline character
        $lineLengths = array_map(function ($line, $index) use ($lines) {
            return mb_strlen($line) + ($index === count($lines) - 1 ? 0 : 1);
        }, $lines, range(0, count($lines) - 1));

        $currentLineIndex = $this->currentLineIndex();

        if ($currentLineIndex === count($lines) - 1) {
            // They're already at the last line, jump them to the last position
            $this->cursorPosition = mb_strlen(implode(PHP_EOL, $lines));

            return;
        }

        // Lines up to and including the current line
        $currentLines = array_slice($lineLengths, 0, $currentLineIndex + 1);

        $currentColumn = CConsole_Prompt_Support_Utils::last($currentLines) - (array_sum($currentLines) - $this->cursorPosition);

        $destinationLineLength = isset($lineLengths[$currentLineIndex + 1]) ? $lineLengths[$currentLineIndex + 1] : CConsole_Prompt_Support_Utils::last($currentLines);

        if ($currentLineIndex + 1 !== count($lines) - 1) {
            $destinationLineLength--;
        }

        $newColumn = min(max(0, $destinationLineLength), $currentColumn);

        $this->cursorPosition = array_sum($currentLines) + $newColumn;
    }

    /**
     * Adjust the visible window to ensure the cursor is always visible.
     *
     * @return void
     */
    protected function adjustVisibleWindow() {
        if (count($this->lines()) < $this->scroll) {
            return;
        }

        $currentLineIndex = $this->currentLineIndex();

        while ($this->firstVisible + $this->scroll <= $currentLineIndex) {
            $this->firstVisible++;
        }

        if ($currentLineIndex === $this->firstVisible - 1) {
            $this->firstVisible = max(0, $this->firstVisible - 1);
        }

        // Make sure there are always the scroll amount visible
        if ($this->firstVisible + $this->scroll > count($this->lines())) {
            $this->firstVisible = count($this->lines()) - $this->scroll;
        }
    }

    /**
     * Get the index of the current line that the cursor is on.
     *
     * @return int
     */
    protected function currentLineIndex() {
        $totalLineLength = 0;

        $result = CConsole_Prompt_Support_Utils::search($this->lines(), function ($line) use (&$totalLineLength) {
            $totalLineLength += mb_strlen($line) + 1;

            return $totalLineLength > $this->cursorPosition;
        });

        return $result === false ? 0 : (int) $result;
    }

    /**
     * Calculate the cursor offset considering wrapped words.
     *
     * @return int
     */
    protected function cursorOffset() {
        $cursorOffset = 0;

        preg_match_all('/\S{' . $this->width . ',}/u', $this->value(), $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $match) {
            if ($this->cursorPosition + $cursorOffset >= $match[1] + mb_strwidth($match[0])) {
                $cursorOffset += (int) floor(mb_strwidth($match[0]) / $this->width);
            }
        }

        return $cursorOffset;
    }

    /**
     * A wrapped version of the placeholder with the virtual cursor.
     *
     * @return string
     */
    protected function wrappedPlaceholderWithCursor() {
        $wrapped = $this->mbWordwrap($this->placeholder, $this->width, PHP_EOL, true);
        $withCursor = $this->addCursor($wrapped, 0);

        return implode(PHP_EOL, array_map(function ($line) {
            return $this->dim($line);
        }, explode(PHP_EOL, $withCursor)));
    }
}
