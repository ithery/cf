<?php
class CPeriod_OpeningHours_Exception_InvalidTimeRangeListException extends CPeriod_OpeningHours_Exception {
    public static function create() {
        return new self('The given list is not a valid list of TimeRange instance containing at least one range.');
    }
}
