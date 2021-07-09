<?php


class CImage_Chart_Helper {
    /**
     * Returns the 1st decimal values (used to correct AA bugs)
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function getFirstDecimal($value) {
        $values = preg_split("/\./", $value);
        if (isset($values[1])) {
            return substr($values[1], 0, 1);
        } else {
            return 0;
        }
    }
}