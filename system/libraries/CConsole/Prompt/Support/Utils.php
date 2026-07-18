<?php

/**
 * @internal
 */
class CConsole_Prompt_Support_Utils {
    /**
     * Determine if all items in an array match a truth test.
     *
     * @param array<array-key, mixed> $values
     * @param Closure                 $callback
     *
     * @return bool
     */
    public static function allMatch(array $values, Closure $callback) {
        foreach ($values as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the last item from an array or null if it doesn't exist.
     *
     * @param array<array-key, mixed> $array
     *
     * @return mixed
     */
    public static function last(array $array) {
        $reversed = array_reverse($array);

        return isset($reversed[0]) ? $reversed[0] : null;
    }

    /**
     * Returns the key of the first element in the array that satisfies the callback.
     *
     * @param array<array-key, mixed> $array
     * @param Closure                 $callback
     *
     * @return int|false|string
     */
    public static function search(array $array, Closure $callback) {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Multi-byte-safe str_pad(). Not a native PHP function on the versions
     * this framework targets (added to core only in PHP 8.3), but the
     * Laravel Prompts source assumes it exists - used in CalloutRenderer and
     * NoteRenderer.
     *
     * @param string $string
     * @param int    $length
     * @param string $padString
     * @param int    $padType
     *
     * @return string
     */
    public static function mbStrPad($string, $length, $padString = ' ', $padType = STR_PAD_RIGHT) {
        $shortfall = $length - mb_strwidth($string);

        if ($shortfall <= 0) {
            return $string;
        }

        $padRepeat = (int) ceil($shortfall / max(1, mb_strwidth($padString)));
        $pad = mb_substr(str_repeat($padString, $padRepeat), 0, $shortfall);

        if ($padType === STR_PAD_LEFT) {
            return $pad . $string;
        }

        if ($padType === STR_PAD_BOTH) {
            $leftLength = (int) floor($shortfall / 2);
            $rightLength = $shortfall - $leftLength;

            return mb_substr($pad, 0, $leftLength) . $string . mb_substr($pad, 0, $rightLength);
        }

        return $string . $pad;
    }
}
