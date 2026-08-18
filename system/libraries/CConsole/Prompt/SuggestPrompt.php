<?php

class CConsole_Prompt_SuggestPrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_HasInfo;
    use CConsole_Prompt_Concerns_Scrolling;
    use CConsole_Prompt_Concerns_Truncation;
    use CConsole_Prompt_Concerns_TypedValue;

    /**
     * The options for the suggest prompt.
     *
     * @var array<string>|Closure
     */
    public $options;

    /**
     * The cache of matches.
     *
     * @var null|array<string>
     */
    protected $matches;

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
     * @var string|Closure
     */
    public $info;

    /**
     * Create a new SuggestPrompt instance.
     *
     * @param string                          $label
     * @param array<string>|CCollection|Closure $options
     * @param string                          $placeholder
     * @param string                          $default
     * @param int                             $scroll
     * @param bool|string                     $required
     * @param mixed                           $validate
     * @param string                          $hint
     * @param null|Closure                    $transform
     * @param string|Closure                  $info
     */
    public function __construct($label, $options, $placeholder = '', $default = '', $scroll = 5, $required = false, $validate = null, $hint = '', $transform = null, $info = '') {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->default = $default;
        $this->scroll = $scroll;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;
        $this->info = $info;

        $this->options = $options instanceof CCollection ? $options->all() : $options;

        $this->initializeScrolling(null);

        $this->on('key', function ($key) {
            if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::SHIFT_TAB, CConsole_Prompt_Key::CTRL_P])) {
                $this->highlightPrevious(count($this->matches()), true);
            } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::TAB, CConsole_Prompt_Key::CTRL_N])) {
                $this->highlightNext(count($this->matches()), true);
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::CTRL_A], $key) !== null) {
                if ($this->highlighted !== null) {
                    $this->highlight(0);
                }
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_E], $key) !== null) {
                if ($this->highlighted !== null) {
                    $this->highlight(count($this->matches()) - 1);
                }
            } elseif ($key === CConsole_Prompt_Key::ENTER) {
                $this->selectHighlighted();
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::LEFT, CConsole_Prompt_Key::LEFT_ARROW, CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW, CConsole_Prompt_Key::CTRL_B, CConsole_Prompt_Key::CTRL_F], $key) !== null) {
                $this->highlighted = null;
            } else {
                $this->highlighted = null;
                $this->matches = null;
                $this->firstVisible = 0;
            }
        });

        $ignore = function ($key) {
            return CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_A, CConsole_Prompt_Key::CTRL_E], $key) !== null && $this->highlighted !== null;
        };

        $this->trackTypedValue($default, true, $ignore);
    }

    /**
     * Get the value of the highlighted option.
     *
     * @return null|string
     */
    public function highlightedValue() {
        if ($this->highlighted === null) {
            return null;
        }

        $matches = $this->matches();

        return isset($matches[$this->highlighted]) ? $matches[$this->highlighted] : null;
    }

    /**
     * Get the entered value with a virtual cursor.
     *
     * @param int $maxWidth
     *
     * @return string
     */
    public function valueWithCursor($maxWidth) {
        if ($this->highlighted !== null) {
            return $this->value() === ''
                ? $this->dim($this->truncate($this->placeholder, $maxWidth))
                : $this->truncate($this->value(), $maxWidth);
        }

        if ($this->value() === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        return $this->addCursor($this->value(), $this->cursorPosition, $maxWidth);
    }

    /**
     * Get options that match the input.
     *
     * @return array<string>
     */
    public function matches() {
        if (is_array($this->matches)) {
            return $this->matches;
        }

        if ($this->options instanceof Closure) {
            $matches = call_user_func($this->options, $this->value());

            return $this->matches = array_values($matches instanceof CCollection ? $matches->all() : $matches);
        }

        return $this->matches = array_values(array_filter($this->options, function ($option) {
            return cstr::startsWith(strtolower($option), strtolower($this->value()));
        }));
    }

    /**
     * The current visible matches.
     *
     * @return array<string>
     */
    public function visible() {
        return array_slice($this->matches(), $this->firstVisible, $this->scroll, true);
    }

    /**
     * Select the highlighted entry.
     *
     * @return void
     */
    protected function selectHighlighted() {
        if ($this->highlighted === null) {
            return;
        }

        $this->typedValue = $this->matches()[$this->highlighted];
    }
}
