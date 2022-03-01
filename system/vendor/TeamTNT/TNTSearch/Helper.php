<?php

namespace TeamTNT\TNTSearch;

class Helper {
    public static function stringEndsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public static function fuzzyMatch($pattern, $items) {
        $fm = new \TeamTNT\TNTSearch\TNTFuzzyMatch();

        return $fm->fuzzyMatch($pattern, $items);
    }

    public static function fuzzyMatchFromFile($pattern, $path) {
        $fm = new \TeamTNT\TNTSearch\TNTFuzzyMatch();

        return $fm->fuzzyMatchFromFile($pattern, $path);
    }
}
