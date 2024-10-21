<?php
trait CTrait_Conditionable {
    /**
     * Apply the callback if the given "value" is truthy.
     *
     * @param mixed         $value
     * @param null|callable $callback
     * @param null|callable $default
     *
     * @return $this|mixed
     */
    public function when($value, $callback = null, $default = null) {
        $value = $value instanceof Closure ? $value($this) : $value;
        if (func_num_args() === 0) {
            return new CBase_HigherOrderWhenProxy($this);
        }
        if (func_num_args() === 1) {
            return (new CBase_HigherOrderWhenProxy($this))->condition($value);
        }

        if ($value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }

    /**
     * Apply the callback if the given "value" is falsy.
     *
     * @param mixed         $value
     * @param null|callable $callback
     * @param null|callable $default
     *
     * @return $this|mixed
     */
    public function unless($value, $callback = null, $default = null) {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (func_num_args() === 0) {
            return (new CBase_HigherOrderWhenProxy($this))->negateConditionOnCapture();
        }

        if (func_num_args() === 1) {
            return (new CBase_HigherOrderWhenProxy($this))->condition(!$value);
        }

        if (!$value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }
}
