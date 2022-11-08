<?php

class CPeriod_OpeningHours_Exception_InvalidDayNameException extends CPeriod_OpeningHours_Exception {
    /**
     * @param string $name
     *
     * @return self
     */
    public static function invalidDayName($name) {
        return new self("Day `{$name}` isn't a valid day name. Valid day names are lowercase english words, e.g. `monday`, `thursday`.");
    }
}
