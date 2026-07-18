<?php

trait CConsole_Prompt_Concerns_Cursor {
    /**
     * Indicates if the cursor has been hidden.
     *
     * @var bool
     */
    protected static $cursorHidden = false;

    /**
     * Hide the cursor.
     *
     * @return void
     */
    public function hideCursor() {
        static::writeDirectly("\e[?25l");

        static::$cursorHidden = true;
    }

    /**
     * Show the cursor.
     *
     * @return void
     */
    public function showCursor() {
        static::writeDirectly("\e[?25h");

        static::$cursorHidden = false;
    }

    /**
     * Restore the cursor if it was hidden.
     *
     * @return void
     */
    public function restoreCursor() {
        if (static::$cursorHidden) {
            $this->showCursor();
        }
    }

    /**
     * Move the cursor.
     *
     * @param int $x
     * @param int $y
     *
     * @return void
     */
    public function moveCursor($x, $y = 0) {
        $sequence = '';

        if ($x < 0) {
            $sequence .= "\e[" . abs($x) . 'D'; // Left
        } elseif ($x > 0) {
            $sequence .= "\e[{$x}C"; // Right
        }

        if ($y < 0) {
            $sequence .= "\e[" . abs($y) . 'A'; // Up
        } elseif ($y > 0) {
            $sequence .= "\e[{$y}B"; // Down
        }

        static::writeDirectly($sequence);
    }

    /**
     * Move the cursor to the given column.
     *
     * @param int $column
     *
     * @return void
     */
    public function moveCursorToColumn($column) {
        static::writeDirectly("\e[{$column}G");
    }

    /**
     * Move the cursor up by the given number of lines.
     *
     * @param int $lines
     *
     * @return void
     */
    public function moveCursorUp($lines) {
        static::writeDirectly("\e[{$lines}A");
    }
}
