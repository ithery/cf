<?php

class CConsole_Prompt_SelectPrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_HasInfo;
    use CConsole_Prompt_Concerns_Scrolling;

    /**
     * The options for the select prompt.
     *
     * @var array<int|string, string>
     */
    public $options;

    /**
     * @var string
     */
    public $label;

    /**
     * @var null|int|string
     */
    public $default;

    /**
     * @var string
     */
    public $hint;

    /**
     * @var string|Closure
     */
    public $info;

    /**
     * Create a new SelectPrompt instance.
     *
     * @param string                                              $label
     * @param array<int|string, string>|CCollection                $options
     * @param null|int|string                                     $default
     * @param int                                                  $scroll
     * @param mixed                                                $validate
     * @param string                                               $hint
     * @param bool|string                                          $required
     * @param null|Closure                                         $transform
     * @param string|Closure                                       $info
     */
    public function __construct($label, $options, $default = null, $scroll = 5, $validate = null, $hint = '', $required = true, $transform = null, $info = '') {
        $this->label = $label;
        $this->default = $default;
        $this->scroll = $scroll;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->required = $required;
        $this->transform = $transform;
        $this->info = $info;

        if ($this->required === false) {
            throw new InvalidArgumentException('Argument [required] must be true or a string.');
        }

        $this->options = $options instanceof CCollection ? $options->all() : $options;

        if ($this->default !== null) {
            if (carr::isList($this->options)) {
                $initial = array_search($this->default, $this->options);
                $this->initializeScrolling($initial !== false ? $initial : 0);
            } else {
                $initial = array_search($this->default, array_keys($this->options));
                $this->initializeScrolling($initial !== false ? $initial : 0);
            }

            $this->scrollToHighlighted(count($this->options));
        } else {
            $this->initializeScrolling(0);
        }

        $this->on('key', function ($key) {
            if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::LEFT, CConsole_Prompt_Key::LEFT_ARROW, CConsole_Prompt_Key::SHIFT_TAB, CConsole_Prompt_Key::CTRL_P, CConsole_Prompt_Key::CTRL_B, 'k', 'h'])) {
                $this->highlightPrevious(count($this->options));
            } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW, CConsole_Prompt_Key::TAB, CConsole_Prompt_Key::CTRL_N, CConsole_Prompt_Key::CTRL_F, 'j', 'l'])) {
                $this->highlightNext(count($this->options));
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::CTRL_A], $key) !== null) {
                $this->highlight(0);
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_E], $key) !== null) {
                $this->highlight(count($this->options) - 1);
            } elseif ($key === CConsole_Prompt_Key::ENTER) {
                $this->submit();
            }
        });
    }

    /**
     * Get the value of the highlighted option.
     *
     * @return null|int|string
     */
    public function highlightedValue() {
        if (carr::isList($this->options)) {
            return isset($this->options[$this->highlighted]) ? $this->options[$this->highlighted] : null;
        }

        $keys = array_keys($this->options);

        return isset($keys[$this->highlighted]) ? $keys[$this->highlighted] : null;
    }

    /**
     * Get the selected value.
     *
     * @return null|int|string
     */
    public function value() {
        if (static::$interactive === false) {
            return $this->default;
        }

        if (carr::isList($this->options)) {
            return isset($this->options[$this->highlighted]) ? $this->options[$this->highlighted] : null;
        }

        return array_keys($this->options)[$this->highlighted];
    }

    /**
     * Get the selected label.
     *
     * @return null|string
     */
    public function label() {
        if (carr::isList($this->options)) {
            return isset($this->options[$this->highlighted]) ? $this->options[$this->highlighted] : null;
        }

        $key = array_keys($this->options)[$this->highlighted];

        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * The currently visible options.
     *
     * @return array<int|string, string>
     */
    public function visible() {
        return array_slice($this->options, $this->firstVisible, $this->scroll, true);
    }

    /**
     * Determine whether the given value is invalid when the prompt is required.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isInvalidWhenRequired($value) {
        return $value === null;
    }
}
