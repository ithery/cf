<?php

class CPeriod_OpeningHours_Exception_OverlappingTimeRangesException extends CPeriod_OpeningHours_Exception {
    public static function forRanges($rangeA, $rangeB) {
        return new self("Time ranges {$rangeA} and {$rangeB} overlap.");
    }
}
