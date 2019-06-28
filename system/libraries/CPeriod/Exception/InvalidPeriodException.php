<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 12:52:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CPeriod_Exception_InvalidPeriod extends CPeriod_Exception {

    public static function startDateCannotBeAfterEndDate(DateTime $startDate, DateTime $endDate) {
        return new static("Start date `{$startDate->format('Y-m-d')}` cannot be after end date `{$endDate->format('Y-m-d')}`.");
    }

}
