<?php

class CConsole_Prompt_MultiSearchPrompt extends CConsole_Prompt {
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
     * Whether the matches are initially a list.
     *
     * @var bool
     */
    protected $isList;

    /**
     * The selected values.
     *
     * @var array<int|string, string>
     */
    public $values = [];

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
     * Create a new MultiSearchPrompt instance.
     *
     * @param string       $label
     * @param Closure      $options
     * @param string       $placeholder
     * @param int          $scroll
     * @param bool|string  $required
     * @param mixed        $validate
     * @param string       $hint
     * @param null|Closure $transform
     * @param string|Closure $info
     */
    public function __construct($label, Closure $options, $placeholder = '', $scroll = 5, $required = false, $validate = null, $hint = '', $transform = null, $info = '') {
        $this->label = $label;
        $this->options = $options;
        $this->placeholder = $placeholder;
        $this->scroll = $scroll;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;
        $this->info = $info;

        $ignore = function ($key) {
            return CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::SPACE, CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_A, CConsole_Prompt_Key::CTRL_E], $key) !== null && $this->highlighted !== null;
        };

        $this->trackTypedValue('', false, $ignore);

        $this->initializeScrolling(null);

        $this->on('key', function ($key) {
            if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::SHIFT_TAB])) {
                $this->highlightPrevious(count($this->matches), true);
            } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::TAB])) {
                $this->highlightNext(count($this->matches), true);
            } elseif (CConsole_Prompt_Key::oneOf(CConsole_Prompt_Key::HOME, $key) !== null) {
                if ($this->highlighted !== null) {
                    $this->highlight(0);
                }
            } elseif (CConsole_Prompt_Key::oneOf(CConsole_Prompt_Key::END, $key) !== null) {
                if ($this->highlighted !== null) {
                    $this->highlight(count($this->matches()) - 1);
                }
            } elseif ($key === CConsole_Prompt_Key::SPACE) {
                if ($this->highlighted !== null) {
                    $this->toggleHighlighted();
                }
            } elseif ($key === CConsole_Prompt_Key::CTRL_A) {
                if ($this->highlighted !== null) {
                    $this->toggleAll();
                }
            } elseif ($key === CConsole_Prompt_Key::CTRL_E) {
                // no-op
            } elseif ($key === CConsole_Prompt_Key::ENTER) {
                $this->submit();
            } elseif (in_array($key, [CConsole_Prompt_Key::LEFT, CConsole_Prompt_Key::LEFT_ARROW, CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW])) {
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
        if ($this->highlighted === null || !is_array($this->matches)) {
            return null;
        }

        if ($this->isList()) {
            return isset($this->matches[$this->highlighted]) ? $this->matches[$this->highlighted] : null;
        }

        $keys = array_keys($this->matches);

        return isset($keys[$this->highlighted]) ? $keys[$this->highlighted] : null;
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

        $matches = call_user_func($this->options, $this->typedValue);

        if (!isset($this->isList) && count($matches) > 0) {
            // This needs to be captured the first time we receive matches so
            // we know what we're dealing with later if matches is empty.
            $this->isList = carr::isList($matches);
        }

        if (!isset($this->isList)) {
            return $this->matches = [];
        }

        if (strlen($this->typedValue) > 0) {
            return $this->matches = $matches;
        }

        return $this->matches = $this->isList
            ? array_merge(array_diff(array_values($this->values), $matches), $matches)
            : array_diff($this->values, $matches) + $matches;
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
     * Toggle all options.
     *
     * @return void
     */
    protected function toggleAll() {
        $isList = $this->isList();
        $values = $this->values;
        $matches = $this->matches;

        $allMatchesSelected = CConsole_Prompt_Support_Utils::allMatch($this->matches, function ($label, $key) use ($isList, $values) {
            return $isList
                ? array_key_exists($label, $values)
                : array_key_exists($key, $values);
        });

        if ($allMatchesSelected) {
            $this->values = array_filter($this->values, function ($value) use ($isList, $matches) {
                return $isList
                    ? !in_array($value, $matches)
                    : !array_key_exists(array_search($value, $matches), $matches);
            });
        } else {
            $this->values = $this->isList()
                ? array_merge($this->values, array_combine(array_values($this->matches), array_values($this->matches)))
                : array_merge($this->values, array_combine(array_keys($this->matches), array_values($this->matches)));
        }
    }

    /**
     * Toggle the highlighted entry.
     *
     * @return void
     */
    protected function toggleHighlighted() {
        if ($this->isList()) {
            $label = $this->matches[$this->highlighted];
            $key = $label;
        } else {
            $key = array_keys($this->matches)[$this->highlighted];
            $label = $this->matches[$key];
        }

        if (array_key_exists($key, $this->values)) {
            unset($this->values[$key]);
        } else {
            $this->values[$key] = $label;
        }
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
     * @return array<int|string>
     */
    public function value() {
        return array_keys($this->values);
    }

    /**
     * Get the selected labels.
     *
     * @return array<string>
     */
    public function labels() {
        return array_values($this->values);
    }

    /**
     * Whether the matches are initially a list.
     *
     * @return bool
     */
    public function isList() {
        return $this->isList;
    }
}
