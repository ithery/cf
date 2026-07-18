<?php

class CConsole_Prompt_Grid extends CConsole_Prompt {
    /**
     * The grid items.
     *
     * @var array<int, string>
     */
    public $items;

    /**
     * The maximum width of the grid.
     *
     * @var int
     */
    public $maxWidth;

    /**
     * Create a new Grid instance.
     *
     * @param array<int, string>|CCollection $items
     * @param null|int                        $maxWidth
     */
    public function __construct($items = [], $maxWidth = null) {
        $this->items = $items instanceof CCollection ? $items->all() : $items;
        $this->maxWidth = $maxWidth !== null ? $maxWidth : (static::terminal()->cols() ?: 80);
    }

    /**
     * Display the grid.
     *
     * @return void
     */
    public function display() {
        $this->prompt();
    }

    /**
     * Display the grid.
     *
     * @return bool
     */
    public function prompt() {
        if ($this->items === []) {
            return true;
        }

        $this->capturePreviousNewLines();

        $this->state = 'submit';

        static::output()->write($this->renderTheme());

        return true;
    }

    /**
     * Get the value of the prompt.
     *
     * @return bool
     */
    public function value() {
        return true;
    }
}
