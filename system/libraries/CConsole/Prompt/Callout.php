<?php

class CConsole_Prompt_Callout extends CConsole_Prompt {
    /**
     * @var string
     */
    public $label;

    /**
     * @var array<int, string|CConsole_Prompt_Elements_ElementContract>|string
     */
    public $content;

    /**
     * @var null|string
     */
    public $type;

    /**
     * @var string
     */
    public $info;

    /**
     * Create a new Callout instance.
     *
     * @param string                                                     $label
     * @param array<int, string|CConsole_Prompt_Elements_ElementContract>|string $content
     * @param null|string                                                $type
     * @param string                                                     $info
     */
    public function __construct($label, $content, $type = null, $info = '') {
        $this->label = $label;
        $this->content = $content;
        $this->type = $type;
        $this->info = $info;
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
     * Display the callout.
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
