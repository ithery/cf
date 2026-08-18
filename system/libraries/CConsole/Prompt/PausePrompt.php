<?php

class CConsole_Prompt_PausePrompt extends CConsole_Prompt {
    /**
     * @var string
     */
    public $message;

    /**
     * Create a new PausePrompt instance.
     *
     * @param string $message
     */
    public function __construct($message = 'Press enter to continue...') {
        $this->message = $message;

        $this->required = false;
        $this->validate = null;

        $this->on('key', function ($key) {
            if ($key === CConsole_Prompt_Key::ENTER) {
                $this->submit();
            }
        });
    }

    /**
     * Get the value of the prompt.
     *
     * @return bool
     */
    public function value() {
        return static::$interactive;
    }
}
