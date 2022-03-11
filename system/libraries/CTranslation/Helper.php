<?php

class CTranslation_Helper {
    /**
     * Expands a single level array with dot notation into a multi-dimensional array.
     *
     * @param array $dotNotationArray
     *
     * @return array
     */
    public static function arrayUndot(array $dotNotationArray) {
        $array = [];
        foreach ($dotNotationArray as $key => $value) {
            // if there is a space after the dot, this could legitimately be
            // a single key and not nested.
            if (count(explode('. ', $key)) > 1) {
                $array[$key] = $value;
            } else {
                carr::set($array, $key, $value);
            }
        }

        return $array;
    }

    /**
     * Determine whether any of the provided strings in the haystack contain the needle.
     *
     * @param array  $haystacks
     * @param string $needle
     *
     * @return bool
     */
    public static function strsContain($haystacks, $needle) {
        $haystacks = (array) $haystacks;

        foreach ($haystacks as $haystack) {
            if (is_array($haystack)) {
                return static::strsContain($haystack, $needle);
            } elseif (cstr::contains(strtolower($haystack), strtolower($needle))) {
                return true;
            }
        }

        return false;
    }
}
