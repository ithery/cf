<?php

trait CConsole_Prompt_Concerns_Scrolling {
    /**
     * The number of items to display before scrolling.
     *
     * @var int
     */
    public $scroll;

    /**
     * The index of the highlighted option.
     *
     * @var null|int
     */
    public $highlighted;

    /**
     * The index of the first visible option.
     *
     * @var int
     */
    public $firstVisible = 0;

    /**
     * Initialize scrolling.
     *
     * @param null|int $highlighted
     *
     * @return void
     */
    protected function initializeScrolling($highlighted = null) {
        $this->highlighted = $highlighted;

        $this->reduceScrollingToFitTerminal();
    }

    /**
     * Reduce the scroll property to fit the terminal height.
     *
     * @return void
     */
    protected function reduceScrollingToFitTerminal() {
        $renderer = $this->getRenderer();
        $reservedLines = $renderer instanceof CConsole_Prompt_Themes_Contracts_Scrolling ? $renderer->reservedLines() : 0;

        $this->scroll = max(1, min($this->scroll, $this->terminal()->lines() - $reservedLines));
    }

    /**
     * Highlight the given index.
     *
     * @param null|int $index
     *
     * @return void
     */
    protected function highlight($index) {
        $this->highlighted = $index;

        if ($this->highlighted === null) {
            return;
        }

        if ($this->highlighted < $this->firstVisible) {
            $this->firstVisible = $this->highlighted;
        } elseif ($this->highlighted > $this->firstVisible + $this->scroll - 1) {
            $this->firstVisible = $this->highlighted - $this->scroll + 1;
        }
    }

    /**
     * Highlight the previous entry, or wrap around to the last entry.
     *
     * @param int  $total
     * @param bool $allowNull
     *
     * @return void
     */
    protected function highlightPrevious($total, $allowNull = false) {
        if ($total === 0) {
            return;
        }

        if ($this->highlighted === null) {
            $this->highlight($total - 1);
        } elseif ($this->highlighted === 0) {
            $this->highlight($allowNull ? null : ($total - 1));
        } else {
            $this->highlight($this->highlighted - 1);
        }
    }

    /**
     * Highlight the next entry, or wrap around to the first entry.
     *
     * @param int  $total
     * @param bool $allowNull
     *
     * @return void
     */
    protected function highlightNext($total, $allowNull = false) {
        if ($total === 0) {
            return;
        }

        if ($this->highlighted === $total - 1) {
            $this->highlight($allowNull ? null : 0);
        } else {
            $this->highlight(($this->highlighted === null ? -1 : $this->highlighted) + 1);
        }
    }

    /**
     * Center the highlighted option.
     *
     * @param int $total
     *
     * @return void
     */
    protected function scrollToHighlighted($total) {
        if ($this->highlighted < $this->scroll) {
            return;
        }

        $remaining = $total - $this->highlighted - 1;
        $halfScroll = (int) floor($this->scroll / 2);
        $endOffset = max(0, $halfScroll - $remaining);

        if ($this->scroll % 2 === 0) {
            $endOffset--;
        }

        $this->firstVisible = $this->highlighted - $halfScroll - $endOffset;
    }
}
