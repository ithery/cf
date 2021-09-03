<?php

/**
 * @internal
 */
interface CTesting_ArgumentFormatterInterface {
    /**
     * Formats the provided array of arguments into
     * an understandable description.
     */
    public function format(array $arguments, bool $recursive = true);
}
