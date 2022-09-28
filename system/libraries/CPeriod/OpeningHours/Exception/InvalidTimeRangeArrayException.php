<?php

class CPeriod_OpeningHours_Exception_InvalidTimeRangeArrayException extends CPeriod_OpeningHours_Exception {
    public static function create() {
        return new self('TimeRange array definition must at least contains an "hours" property.');
    }
}
