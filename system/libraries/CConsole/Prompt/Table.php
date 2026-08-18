<?php

class CConsole_Prompt_Table extends CConsole_Prompt {
    /**
     * The table headers.
     *
     * @var array<int, array<int, string>|string>
     */
    public $headers;

    /**
     * The table rows.
     *
     * @var array<int, array<int, string>>
     */
    public $rows;

    /**
     * Create a new Table instance.
     *
     * @param array<int, array<int, string>|string>|CCollection $headers
     * @param null|array<int, array<int, string>>|CCollection    $rows
     */
    public function __construct($headers = [], $rows = null) {
        if ($rows === null) {
            $rows = $headers;
            $headers = [];
        }

        $this->headers = $headers instanceof CCollection ? $headers->all() : $headers;
        $this->rows = $rows instanceof CCollection ? $rows->all() : $rows;
    }

    /**
     * Display the table.
     *
     * @return void
     */
    public function display() {
        $this->prompt();
    }

    /**
     * Display the table.
     *
     * @return bool
     */
    public function prompt() {
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
