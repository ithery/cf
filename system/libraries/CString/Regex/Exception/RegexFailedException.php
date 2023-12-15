<?php

class CString_Regex_Exception_RegexFailedException extends Exception {
    /**
     * @param string $pattern
     * @param string $subject
     * @param string $message
     *
     * @return static
     */
    public static function match(string $pattern, string $subject, string $message) {
        $subject = static::trimString($subject);

        return new static("Error matching pattern `{$pattern}` with subject `{$subject}`. {$message}");
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @param string $message
     *
     * @return static
     */
    public static function replace(string $pattern, string $subject, string $message) {
        $subject = static::trimString($subject);

        return new static("Error replacing pattern `{$pattern}` in subject `{$subject}`. {$message}");
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @param mixed  $group
     *
     * @return static
     */
    public static function groupDoesntExist(string $pattern, string $subject, $group) {
        return new static("Pattern `{$pattern}` with subject `{$subject}` didn't capture a group named {$group}");
    }

    protected static function trimString(string $string): string {
        if (strlen($string) < 40) {
            return $string;
        }

        return substr($string, 0, 40) . '...';
    }
}
