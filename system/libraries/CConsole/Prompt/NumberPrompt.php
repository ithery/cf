<?php

class CConsole_Prompt_NumberPrompt extends CConsole_Prompt {
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
     * @var null|int
     */
    public $min;

    /**
     * @var null|int
     */
    public $max;

    /**
     * @var null|int
     */
    public $step;

    /**
     * Create a new NumberPrompt instance.
     *
     * @param string       $label
     * @param string       $placeholder
     * @param string       $default
     * @param bool|string  $required
     * @param mixed        $validate
     * @param string       $hint
     * @param null|Closure $transform
     * @param null|int     $min
     * @param null|int     $max
     * @param null|int     $step
     */
    public function __construct($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $transform = null, $min = null, $max = null, $step = null) {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->default = $default;
        $this->required = $required;
        $this->validate = $validate;
        $this->hint = $hint;
        $this->transform = $transform;
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;

        $this->trackTypedValue($default);

        $this->step = max(1, $this->step === null ? 1 : $this->step);
        if ($this->min === null) {
            $this->min = PHP_INT_MIN;
        }
        if ($this->max === null) {
            $this->max = PHP_INT_MAX;
        }

        $this->validate = $this->wrapValidation($this->validate);

        $this->on('key', function ($key) {
            if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW])) {
                $this->increaseValue();
            } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW])) {
                $this->decreaseValue();
            }
        });
    }

    /**
     * @param mixed $validate
     *
     * @return callable
     */
    protected function wrapValidation($validate) {
        return function ($value) use ($validate) {
            if ($value !== '' && !is_numeric($value)) {
                return 'Must be a number';
            }

            if (is_numeric($value)) {
                if ($value < $this->min) {
                    return 'Must be at least ' . $this->min;
                }

                if ($value > $this->max) {
                    return 'Must be less than ' . $this->max;
                }
            }

            if (!$validate && !isset(static::$validateUsing)) {
                return null;
            }

            if (is_callable($validate)) {
                return call_user_func($validate, $value);
            }

            if (isset(static::$validateUsing)) {
                return call_user_func(static::$validateUsing, $this);
            }

            throw new RuntimeException('The validation logic is missing.');
        };
    }

    /**
     * Increase the value of the prompt by the step.
     *
     * @return void
     */
    protected function increaseValue() {
        if ($this->typedValue === '') {
            $this->typedValue = (string) ($this->min === PHP_INT_MIN ? 1 : $this->min);
            $this->cursorPosition++;

            return;
        }

        if (is_numeric($this->typedValue)) {
            $previousValueLength = mb_strlen($this->typedValue);

            $this->typedValue = (string) min($this->max, (int) $this->typedValue + $this->step);

            if (mb_strlen($this->typedValue) > $previousValueLength) {
                $this->cursorPosition++;
            }
        }
    }

    /**
     * Decrease the value of the prompt by the step.
     *
     * @return void
     */
    protected function decreaseValue() {
        if ($this->typedValue === '') {
            $this->typedValue = (string) ($this->max === PHP_INT_MAX ? 0 : $this->max);
            $this->cursorPosition++;

            return;
        }

        if (is_numeric($this->typedValue)) {
            $previousValueLength = mb_strlen($this->typedValue);

            $this->typedValue = (string) max($this->min, (int) $this->typedValue - $this->step);

            if (mb_strlen($this->typedValue) < $previousValueLength) {
                $this->cursorPosition--;
            }
        }
    }

    /**
     * @return int|string
     */
    public function value() {
        if (is_numeric($this->typedValue)) {
            return (int) $this->typedValue;
        }

        return $this->typedValue;
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

        return $this->addCursor((string) $this->value(), $this->cursorPosition, $maxWidth);
    }
}
