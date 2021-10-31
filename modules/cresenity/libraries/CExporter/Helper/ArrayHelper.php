<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Helper_ArrayHelper {

    /**
     * @param array $array
     *
     * @return array
     */
    public static function ensureMultipleRows(array $array) {
        if (static::hasMultipleRows($array)) {
            return $array;
        }

        return [$array];
    }

    /**
     * Only have multiple rows, if each
     * element in the array is an array itself.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function hasMultipleRows(array $array) {
        return count($array) === count(array_filter($array, 'is_array'));
    }

}
