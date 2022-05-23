<?php

class CPeriod_Exception_CannotCeilLowerPrecisionException extends CPeriod_Exception {
    /**
     * @param CPeriod_Precision $a
     * @param CPeriod_Precision $b
     *
     * @return CPeriod_Exception_CannotCeilLowerPrecisionException
     */
    public static function precisionIsLower(CPeriod_Precision $a, CPeriod_Precision $b) {
        $from = self::unitName($a);
        $to = self::unitName($b);

        return new self("Cannot get the latest ${from} of a ${to}.");
    }

    protected static function unitName(CPeriod_Precision $precision) {
        $matchMap = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        return carr::get($matchMap, $precision->intervalName());
    }
}
