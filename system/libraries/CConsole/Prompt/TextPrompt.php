<?php

class CConsole_Prompt_TextPrompt extends CConsole_Prompt {
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
    public $default;

    /**
     * @var string
     */
    public $hint;

    /**
     * Create a new TextPrompt instance.
     *
     * @param string      $label
     * @param string      $placeholder
     * @param string      $default
     * @param bool|string $required
     * @param mixed       $validate
     * @param string      $hint
     * @param null|Closure $transform
     */
    public function __construct($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $transform = null) {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->default = $default;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;

        $this->trackTypedValue($default);
    }

    /**
     * Get the entered value with a virtual cursor.
     *
     * @param int $maxWidth
     *
     * @return string
     */
    public function valueWithCursor($maxWidth) {
        if ($this->value() === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        return $this->addCursor($this->value(), $this->cursorPosition, $maxWidth);
    }
}
