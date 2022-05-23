<?php
trait CTrait_Conditionable {
    /**
     * Apply the callback if the given "value" is truthy.
     *
     * @param mixed         $value
     * @param callable      $callback
     * @param null|callable $default
     *
     * @return $this|mixed
     */
    public function when($value, $callback, $default = null) {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (!$callback) {
            return new CBase_HigherOrderWhenProxy($this, $value);
        }

        if ($value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * Apply the callback if the given "value" is falsy.
     *
     * @param mixed         $value
     * @param callable      $callback
     * @param null|callable $default
     *
     * @return $this|mixed
     */
    public function unless($value, $callback, $default = null) {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (!$callback) {
            return new CBase_HigherOrderWhenProxy($this, !$value);
        }

        if (!$value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }
}
