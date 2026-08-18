<?php

trait CConsole_Prompt_Concerns_Interactivity {
    /**
     * Whether to render the prompt interactively.
     *
     * @var bool
     */
    protected static $interactive;

    /**
     * Set interactive mode.
     *
     * @param bool $interactive
     *
     * @return void
     */
    public static function interactive($interactive = true) {
        static::$interactive = $interactive;
    }

    /**
     * Return the default value if it passes validation.
     *
     * @return mixed
     */
    protected function default() {
        $default = $this->value();

        $this->validate($default);

        if ($this->state === 'error') {
            throw new CConsole_Prompt_Exceptions_NonInteractiveValidationException($this->error);
        }

        return $default;
    }
}
