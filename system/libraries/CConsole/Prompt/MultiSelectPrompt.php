<?php

class CConsole_Prompt_MultiSelectPrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_HasInfo;
    use CConsole_Prompt_Concerns_Scrolling;

    /**
     * The options for the multi-select prompt.
     *
     * @var array<int|string, string>
     */
    public $options;

    /**
     * The default values the multi-select prompt.
     *
     * @var array<int|string>
     */
    public $default;

    /**
     * The selected values.
     *
     * @var array<int|string>
     */
    protected $values = [];

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $hint;

    /**
     * @var string|Closure
     */
    public $info;

    /**
     * Create a new MultiSelectPrompt instance.
     *
     * @param string                                       $label
     * @param array<int|string, string>|CCollection          $options
     * @param array<int|string>|CCollection                  $default
     * @param int                                            $scroll
     * @param bool|string                                    $required
     * @param mixed                                          $validate
     * @param string                                         $hint
     * @param null|Closure                                   $transform
     * @param string|Closure                                 $info
     */
    public function __construct($label, $options, $default = [], $scroll = 5, $required = false, $validate = null, $hint = '', $transform = null, $info = '') {
        $this->label = $label;
        $this->scroll = $scroll;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;
        $this->info = $info;

        $this->options = $options instanceof CCollection ? $options->all() : $options;
        $this->default = $default instanceof CCollection ? $default->all() : $default;
        $this->values = $this->default;

        $this->initializeScrolling(0);

        $this->on('key', function ($key) {
            if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::LEFT, CConsole_Prompt_Key::LEFT_ARROW, CConsole_Prompt_Key::SHIFT_TAB, CConsole_Prompt_Key::CTRL_P, CConsole_Prompt_Key::CTRL_B, 'k', 'h'])) {
                $this->highlightPrevious(count($this->options));
            } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW, CConsole_Prompt_Key::TAB, CConsole_Prompt_Key::CTRL_N, CConsole_Prompt_Key::CTRL_F, 'j', 'l'])) {
                $this->highlightNext(count($this->options));
            } elseif (CConsole_Prompt_Key::oneOf(CConsole_Prompt_Key::HOME, $key) !== null) {
                $this->highlight(0);
            } elseif (CConsole_Prompt_Key::oneOf(CConsole_Prompt_Key::END, $key) !== null) {
                $this->highlight(count($this->options) - 1);
            } elseif ($key === CConsole_Prompt_Key::SPACE) {
                $this->toggleHighlighted();
            } elseif ($key === CConsole_Prompt_Key::CTRL_A) {
                $this->toggleAll();
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
        if ($this->highlighted === null) {
            return null;
        }

        if (carr::isList($this->options)) {
            return isset($this->options[$this->highlighted]) ? $this->options[$this->highlighted] : null;
        }

        $keys = array_keys($this->options);

        return isset($keys[$this->highlighted]) ? $keys[$this->highlighted] : null;
    }

    /**
     * Get the selected values.
     *
     * @return array<int|string>
     */
    public function value() {
        return array_values($this->values);
    }

    /**
     * Get the selected labels.
     *
     * @return array<string>
     */
    public function labels() {
        if (carr::isList($this->options)) {
            return array_map(function ($value) {
                return (string) $value;
            }, $this->values);
        }

        return array_values(array_intersect_key($this->options, array_flip($this->values)));
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
     * Check whether the value is currently highlighted.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isHighlighted($value) {
        if (carr::isList($this->options)) {
            return $this->options[$this->highlighted] === $value;
        }

        return array_keys($this->options)[$this->highlighted] === $value;
    }

    /**
     * Check whether the value is currently selected.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isSelected($value) {
        return in_array($value, $this->values);
    }

    /**
     * Toggle all options.
     *
     * @return void
     */
    protected function toggleAll() {
        if (count($this->values) === count($this->options)) {
            $this->values = [];
        } else {
            $this->values = carr::isList($this->options)
                ? array_values($this->options)
                : array_keys($this->options);
        }
    }

    /**
     * Toggle the highlighted entry.
     *
     * @return void
     */
    protected function toggleHighlighted() {
        $value = carr::isList($this->options)
            ? $this->options[$this->highlighted]
            : array_keys($this->options)[$this->highlighted];

        if (in_array($value, $this->values)) {
            $this->values = array_filter($this->values, function ($v) use ($value) {
                return $v !== $value;
            });
        } else {
            $this->values[] = $value;
        }
    }
}
