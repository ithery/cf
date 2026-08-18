<?php

trait CConsole_Prompt_Concerns_Truncation {
    /**
     * Truncate a value with an ellipsis if it exceeds the given width.
     *
     * @param string $string
     * @param int    $width
     *
     * @return string
     */
    protected function truncate($string, $width) {
        if ($width <= 0) {
            throw new InvalidArgumentException("Width [{$width}] must be greater than zero.");
        }

        return mb_strwidth($string) <= $width ? $string : (mb_strimwidth($string, 0, $width - 1) . '…');
    }
}
