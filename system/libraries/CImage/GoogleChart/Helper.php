<?php

class CImage_GoogleChart_Helper {
    public static function getMaxOfArray($arrayToCheck) {
        $maxValue = 0;

        foreach ($arrayToCheck as $temp) {
            if (is_array($temp)) {
                $maxValue = max($maxValue, static::getMaxOfArray($temp));
            } else {
                $maxValue = max($maxValue, $temp);
            }
        }
        return $maxValue;
    }
}
