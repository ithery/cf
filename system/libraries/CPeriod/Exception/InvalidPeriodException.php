<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
class CPeriod_Exception_InvalidPeriod extends CPeriod_Exception {
    public static function startDateCannotBeAfterEndDate(DateTime $startDate, DateTime $endDate) {
        return new static("Start date `{$startDate->format('Y-m-d')}` cannot be after end date `{$endDate->format('Y-m-d')}`.");
    }
}
