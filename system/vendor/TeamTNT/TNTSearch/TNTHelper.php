<?php

namespace TeamTNT\TNTSearch;

class TNTHelper {
    public static function fuzzyMatch($pattern, $items)
    {
        $fm = new \TeamTNT\TNTSearch\TNTFuzzyMatch;
        return $fm->fuzzyMatch($pattern, $items);
    }

    public static function fuzzyMatchFromFile($pattern, $path)
    {
        $fm = new \TeamTNT\TNTSearch\TNTFuzzyMatch;
        return $fm->fuzzyMatchFromFile($pattern, $path);
    }
}
