<?php

class CConsole_Prompt_SearchPrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_HasInfo;
    use CConsole_Prompt_Concerns_Scrolling;
    use CConsole_Prompt_Concerns_Truncation;
    use CConsole_Prompt_Concerns_TypedValue;

    /**
     * The cached matches.
     *
     * @var null|array<int|string, string>
     */
    protected $matches;

    /**
     * @var string
     */
    public $label;

    /**
     * @var Closure
     */
    public $options;

    /**
     * @var string
     */
    public $placeholder;

    /**
     * @var string
     */
    public $hint;

    /**
     * @var string|Closure
     */
    public $info;

    /**
     * Create a new SearchPrompt instance.
     *
     * @param string       $label
     * @param Closure      $options
     * @param string       $placeholder
     * @param int          $scroll
     * @param mixed        $validate
     * @param string       $hint
     * @param bool|string  $required
     * @param null|Closure $transform
     * @param string|Closure $info
     */
    public function __construct($label, Closure $options, $placeholder = '', $scroll = 5, $validate = null, $hint = '', $required = true, $transform = null, $info = '') {
        $this->label = $label;
        $this->options = $options;
        $this->placeholder = $placeholder;
        $this->scroll = $scroll;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->required = $required;
        $this->transform = $transform;
        $this->info = $info;

        if ($this->required === false) {
            throw new InvalidArgumentException('Argument [required] must be true or a string.');
        }

        $ignore = function ($key) {
            return CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_A, CConsole_Prompt_Key::CTRL_E], $key) !== null && $this->highlighted !== null;
        };

        $this->trackTypedValue('', false, $ignore);

        $this->initializeScrolling(null);

        $this->on('key', function ($key) {
            if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::SHIFT_TAB, CConsole_Prompt_Key::CTRL_P])) {
                $this->highlightPrevious(count($this->matches), true);
            } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::TAB, CConsole_Prompt_Key::CTRL_N])) {
                $this->highlightNext(count($this->matches), true);
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::CTRL_A], $key) !== null) {
                if ($this->highlighted !== null) {
                    $this->highlight(0);
                }
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_E], $key) !== null) {
                if ($this->highlighted !== null) {
                    $this->highlight(count($this->matches()) - 1);
                }
            } elseif ($key === CConsole_Prompt_Key::ENTER) {
                if ($this->highlighted !== null) {
                    $this->submit();
                } else {
                    $this->search();
                }
            } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::LEFT, CConsole_Prompt_Key::LEFT_ARROW, CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW, CConsole_Prompt_Key::CTRL_B, CConsole_Prompt_Key::CTRL_F], $key) !== null) {
                $this->highlighted = null;
            } else {
                $this->search();
            }
        });
    }

    /**
     * Get the value of the highlighted option.
     *
     * @return null|int|string
     */
    public function highlightedValue() {
        return $this->value();
    }

    /**
     * Perform the search.
     *
     * @return void
     */
    protected function search() {
        $this->state = 'searching';
        $this->highlighted = null;
        $this->render();
        $this->matches = null;
        $this->firstVisible = 0;
        $this->state = 'active';
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
            return $this->typedValue === ''
                ? $this->dim($this->truncate($this->placeholder, $maxWidth))
                : $this->truncate($this->typedValue, $maxWidth);
        }

        if ($this->typedValue === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        return $this->addCursor($this->typedValue, $this->cursorPosition, $maxWidth);
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

        return $this->matches = call_user_func($this->options, $this->typedValue);
    }

    /**
     * The currently visible matches.
     *
     * @return array<string>
     */
    public function visible() {
        return array_slice($this->matches(), $this->firstVisible, $this->scroll, true);
    }

    /**
     * Get the current search query.
     *
     * @return string
     */
    public function searchValue() {
        return $this->typedValue;
    }

    /**
     * Get the selected value.
     *
     * @return null|int|string
     */
    public function value() {
        if ($this->matches === null || $this->highlighted === null) {
            return null;
        }

        return carr::isList($this->matches)
            ? $this->matches[$this->highlighted]
            : array_keys($this->matches)[$this->highlighted];
    }

    /**
     * Get the selected label.
     *
     * @return null|string
     */
    public function label() {
        $key = array_keys($this->matches)[$this->highlighted];

        return isset($this->matches[$key]) ? $this->matches[$key] : null;
    }
}
