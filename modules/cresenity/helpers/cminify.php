<?php

//@codingStandardsIgnoreStart
class cminify {
    /**
     * Minimizes and compresses the provided string. Removes comments, tabs, spaces, and newlines.
     * Warning: Does not work with double slash (//) comments
     *
     * @param string $buffer The string to minimize.
     *
     * @return string The minimized string.
     */
    public static function str($buffer) {
        // remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        // remove tabs, spaces, newlines, etc
        $buffer = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '     '], '', $buffer);
        // remove other spaces before/after
        $buffer = preg_replace(['(( )+{)', '({( )+)'], '{', $buffer);
        $buffer = preg_replace(['(( )+})', '(}( )+)', '(;( )*})'], '}', $buffer);
        $buffer = preg_replace(['(;( )+)', '(( )+;)'], ';', $buffer);
        // return string
        return $buffer;
    }

    /**
     * Minimizes and compresses the provided $filename. Removes comments, tabs, spaces, and newlines.
     * Warning: Does not work with double slash (//) comments
     *
     * @param string $filename The string to minimize.
     *
     * @return int Returns the number of bytes that were written to the file, or FALSE on failure.
     */
    public static function file($filename) {
        // Get the original contents of the file
        $buffer = file_get_contents($filename);
        // minimize the contents
        $buffer = cminify::str($buffer);
        // store minimized contents back into file and return
        return file_put_contents($filename, $buffer);
    }
}
