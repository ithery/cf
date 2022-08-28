<?php
class CPeriod_OpeningHours_Exception_InvalidTimeStringException extends CPeriod_OpeningHours_Exception {
    public static function forString($string) {
        return new self("The string `{$string}` isn't a valid time string. A time string must be a formatted as `H:i`, e.g. `06:00`, `18:00`.");
    }
}
