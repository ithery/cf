<?php

class CConsole_Prompt_Note extends CConsole_Prompt {
    /**
     * @var string
     */
    public $message;

    /**
     * @var null|string
     */
    public $type;

    /**
     * Create a new Note instance.
     *
     * @param string      $message
     * @param null|string $type
     */
    public function __construct($message, $type = null) {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Display the note.
     *
     * @return void
     */
    public function display() {
        $this->prompt();
    }

    /**
     * Display the note.
     *
     * @return bool
     */
    public function prompt() {
        $this->capturePreviousNewLines();

        if (static::shouldFallback()) {
            return $this->fallback();
        }

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
