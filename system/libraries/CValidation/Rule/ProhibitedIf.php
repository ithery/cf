<?php

class CValidation_Rule_ProhibitedIf {
    /**
     * The condition that validates the attribute.
     *
     * @var \Closure|bool
     */
    public $condition;

    /**
     * Create a new prohibited validation rule based on a condition.
     *
     * @param \Closure|bool $condition
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct($condition) {
        if ($condition instanceof Closure || is_bool($condition)) {
            $this->condition = $condition;
        } else {
            throw new InvalidArgumentException('The provided condition must be a callable or boolean.');
        }
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString() {
        if (is_callable($this->condition)) {
            return call_user_func($this->condition) ? 'prohibited' : '';
        }

        return $this->condition ? 'prohibited' : '';
    }
}
