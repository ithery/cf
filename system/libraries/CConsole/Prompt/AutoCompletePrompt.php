<?php

class CConsole_Prompt_AutoCompletePrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_TypedValue;

    /**
     * The options for the autocomplete prompt.
     *
     * @var array<string>|Closure
     */
    public $options;

    /**
     * @var string
     */
    protected $match = '';

    /**
     * @var int
     */
    protected $highlighted = 0;

    /**
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
     * Create a new AutoCompletePrompt instance.
     *
     * @param string                          $label
     * @param array<string>|CCollection|Closure $options
     * @param string                          $placeholder
     * @param string                          $default
     * @param bool|string                     $required
     * @param mixed                           $validate
     * @param string                          $hint
     * @param null|Closure                    $transform
     */
    public function __construct($label, $options = [], $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $transform = null) {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->default = $default;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;

        $this->options = $options instanceof CCollection ? $options->all() : $options;

        $this->on('key', function ($key) {
            if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW])) {
                $matches = $this->matches();

                if (count($matches) > 0) {
                    $this->highlighted = ($this->highlighted - 1 + count($matches)) % count($matches);
                }

                return;
            }

            if (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW])) {
                $matches = $this->matches();

                if (count($matches) > 0) {
                    $this->highlighted = ($this->highlighted + 1) % count($matches);
                }

                return;
            }

            if ($key === CConsole_Prompt_Key::TAB && $this->cursorPosition >= mb_strlen($this->typedValue)) {
                $match = $this->getMatch();

                if ($match !== '' && mb_strlen($match) > mb_strlen($this->value())) {
                    // Ghost text is showing — accept it
                    $this->typedValue = $match;
                    $this->cursorPosition = mb_strlen($match);
                } else {
                    // No ghost text — request suggestions
                    $this->matches = null;
                    $this->highlighted = 0;
                }

                return;
            }

            if (in_array($key, [CConsole_Prompt_Key::RIGHT, CConsole_Prompt_Key::RIGHT_ARROW]) && $this->cursorPosition >= mb_strlen($this->typedValue)) {
                $match = $this->getMatch();

                if ($match !== '') {
                    $this->typedValue = $match;
                    $this->cursorPosition = mb_strlen($match);
                }

                return;
            }

            // Any other key resets the highlight and match cache
            $this->highlighted = 0;
            $this->matches = null;
        });

        $ignore = function ($key) {
            return in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW]);
        };

        $this->trackTypedValue($default, true, $ignore);
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

        $this->match = $this->getMatch();

        $ghostText = '';

        if ($this->match !== '' && mb_strlen($this->match) > mb_strlen($this->value())) {
            $ghostText = mb_substr($this->match, mb_strlen($this->value()));
        }

        // When cursor is at the end and there's ghost text, make the first
        // ghost character the inverted cursor so it flows naturally.
        if ($ghostText !== '' && $this->cursorPosition >= mb_strlen($this->value())) {
            $cursorChar = mb_substr($ghostText, 0, 1);
            $remainingGhost = mb_substr($ghostText, 1);

            return $this->value()
                . $this->inverse($cursorChar)
                . $this->dim($remainingGhost);
        }

        return $this->addCursor(
            $this->value(),
            $this->cursorPosition,
            $maxWidth
        ) . $this->dim($ghostText);
    }

    /**
     * Get the current matches for the typed value.
     *
     * @return array<string>
     */
    public function matches() {
        if (is_array($this->matches)) {
            return $this->matches;
        }

        if ($this->options instanceof Closure) {
            $options = call_user_func($this->options, $this->value());

            return $this->matches = array_values($options instanceof CCollection ? $options->all() : $options);
        }

        return $this->matches = array_values(array_filter(
            $this->options,
            function ($option) {
                return cstr::startsWith(strtolower($option), strtolower($this->value()));
            }
        ));
    }

    /**
     * Get the current match.
     *
     * @return string
     */
    protected function getMatch() {
        $matches = $this->matches();

        return isset($matches[$this->highlighted]) ? $matches[$this->highlighted] : '';
    }
}
