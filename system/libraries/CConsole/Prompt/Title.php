<?php

class CConsole_Prompt_Title extends CConsole_Prompt {
    /**
     * @var string
     */
    public $title;

    /**
     * @param string $title
     */
    public function __construct($title) {
        $this->title = $title;
    }

    /**
     * Update the title of the terminal.
     *
     * @return bool
     */
    public function prompt() {
        $this->writeDirectly($this->renderTheme());

        return true;
    }

    /**
     * Update the title of the terminal.
     *
     * @return void
     */
    public function display() {
        $this->prompt();
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
