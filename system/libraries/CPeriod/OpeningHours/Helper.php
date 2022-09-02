<?php

class CPeriod_OpeningHours_Helper {
    /**
     * @param array    $array
     * @param callable $callback
     *
     * @return array
     */
    public static function flatMap(array $array, $callback) {
        $mapped = carr::map($array, $callback);

        $flattened = [];

        foreach ($mapped as $item) {
            if (is_array($item)) {
                $flattened = array_merge($flattened, $item);
            } else {
                $flattened[] = $item;
            }
        }

        return $flattened;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public static function createUniquePairs(array $array) {
        $pairs = [];

        while ($a = array_shift($array)) {
            foreach ($array as $b) {
                $pairs[] = [$a, $b];
            }
        }

        return $pairs;
    }
}
