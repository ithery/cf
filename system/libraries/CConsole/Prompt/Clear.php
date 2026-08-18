<?php

class CConsole_Prompt_Clear extends CConsole_Prompt {
    /**
     * Clear the terminal.
     *
     * @return bool
     */
    public function prompt() {
        // Fill the previous newline count so subsequent prompts won't add padding.
        static::output()->write(PHP_EOL . PHP_EOL);

        $this->writeDirectly($this->renderTheme());

        return true;
    }

    /**
     * Clear the terminal.
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
