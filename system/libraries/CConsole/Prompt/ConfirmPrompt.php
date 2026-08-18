<?php

class CConsole_Prompt_ConfirmPrompt extends CConsole_Prompt {
    /**
     * Whether the prompt has been confirmed.
     *
     * @var bool
     */
    public $confirmed;

    /**
     * @var string
     */
    public $label;

    /**
     * @var bool
     */
    public $default;

    /**
     * @var string
     */
    public $yes;

    /**
     * @var string
     */
    public $no;

    /**
     * @var string
     */
    public $hint;

    /**
     * Create a new ConfirmPrompt instance.
     *
     * @param string       $label
     * @param bool         $default
     * @param string       $yes
     * @param string       $no
     * @param bool|string  $required
     * @param mixed        $validate
     * @param string       $hint
     * @param null|Closure $transform
     */
    public function __construct($label, $default = true, $yes = 'Yes', $no = 'No', $required = false, $validate = null, $hint = '', $transform = null) {
        $this->label = $label;
        $this->default = $default;
        $this->yes = $yes;
        $this->no = $no;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;

        $this->confirmed = $default;

        $toggleKeys = [
            CConsole_Prompt_Key::TAB, CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW,
            CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::LEFT,
            CConsole_Prompt_Key::LEFT_ARROW, CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW,
            CConsole_Prompt_Key::CTRL_P, CConsole_Prompt_Key::CTRL_F, CConsole_Prompt_Key::CTRL_N,
            CConsole_Prompt_Key::CTRL_B, 'h', 'j', 'k', 'l',
        ];

        $this->on('key', function ($key) use ($toggleKeys) {
            if ($key === 'y') {
                $this->confirmed = true;
            } elseif ($key === 'n') {
                $this->confirmed = false;
            } elseif (in_array($key, $toggleKeys)) {
                $this->confirmed = !$this->confirmed;
            } elseif ($key === CConsole_Prompt_Key::ENTER) {
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
        return $this->confirmed;
    }

    /**
     * Get the label of the selected option.
     *
     * @return string
     */
    public function label() {
        return $this->confirmed ? $this->yes : $this->no;
    }
}
