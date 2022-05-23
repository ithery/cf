<?php

namespace CarbonV3;

class Helper {
    public static function strEndsWith($haystack, $needles) {
        if (\function_exists('str_ends_with')) {
            return str_ends_with($haystack, $needles);
        }

        foreach ((array) $needles as $needle) {
            if ($needle !== '' && $needle !== null
                && substr($haystack, -strlen($needle)) === (string) $needle
            ) {
                return true;
            }
        }

        return false;
    }
}
