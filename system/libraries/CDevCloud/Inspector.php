<?php
/**
 * @see CDevCloud
 */
class CDevCloud_Inspector extends \Inspector\Inspector {
    /**
     * A wrap to monitor a function execution called by CF Container.
     *
     * @param mixed $callback
     * @param array $parameters
     *
     * @throws \Throwable
     *
     * @return mixed|void
     */
    public function call($callback, array $parameters = []) {
        if (is_string($callback)) {
            $label = $callback;
        } elseif (is_array($callback)) {
            $label = get_class($callback[0]) . '@' . $callback[1];
        } else {
            $label = 'closure';
        }

        return $this->addSegment(function ($segment) use ($callback, $parameters) {
            $segment->addContext('Parameters', $parameters);

            return c::container()->call($callback, $parameters);
        }, 'method', $label, true);
    }
}
