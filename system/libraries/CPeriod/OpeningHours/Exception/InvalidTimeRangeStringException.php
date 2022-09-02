<?php
class CPeriod_OpeningHours_Exception_InvalidTimeRangeStringException extends CPeriod_OpeningHours_Exception {
    public static function forString($string) {
        return new self("The string `{$string}` isn't a valid time range string. A time string must be a formatted as `H:i-H:i`, e.g. `09:00-18:00`.");
    }
}
