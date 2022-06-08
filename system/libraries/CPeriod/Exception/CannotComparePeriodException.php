<?php

class CPeriod_Exception_CannotComparePeriodException extends Exception {
    /**
     * @return CPeriod_Exception_CannotComparePeriodException
     */
    public static function precisionDoesNotMatch() {
        return new self("Cannot compare two periods whose precision doesn't match.");
    }
}
