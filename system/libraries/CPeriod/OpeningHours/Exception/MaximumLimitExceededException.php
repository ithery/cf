<?php

class CPeriod_OpeningHours_Exception_MaximumLimitExceededException extends CPeriod_OpeningHours_Exception {
    /**
     * @param string $string
     *
     * @return self
     */
    public static function forString($string) {
        return new self($string);
    }
}
