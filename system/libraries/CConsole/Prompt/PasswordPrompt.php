<?php

class CConsole_Prompt_PasswordPrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_TypedValue;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $placeholder;

    /**
     * @var string
     */
    public $hint;

    /**
     * Create a new PasswordPrompt instance.
     *
     * @param string       $label
     * @param string       $placeholder
     * @param bool|string  $required
     * @param mixed        $validate
     * @param string       $hint
     * @param null|Closure $transform
     */
    public function __construct($label, $placeholder = '', $required = false, $validate = null, $hint = '', $transform = null) {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;

        $this->trackTypedValue();
    }

    /**
     * Get a masked version of the entered value.
     *
     * @return string
     */
    public function masked() {
        return str_repeat('•', mb_strlen($this->value()));
    }

    /**
     * Get the masked value with a virtual cursor.
     *
     * @param int $maxWidth
     *
     * @return string
     */
    public function maskedWithCursor($maxWidth) {
        if ($this->value() === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        return $this->addCursor($this->masked(), $this->cursorPosition, $maxWidth);
    }
}
