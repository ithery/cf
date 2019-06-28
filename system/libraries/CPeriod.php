<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 12:50:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CPeriod {

    /** @var \DateTime */
    public $startDate;

    /** @var \DateTime */
    public $endDate;

    public static function create(DateTime $startDate, DateTime $endDate) {
        return new static($startDate, $endDate);
    }

    public static function lifetime($minimumDate = null) {
        $endDate = CCarbon::today();
        $startDate = CCarbon::createFromTimestamp(0);
        return new static($startDate, $endDate);
    }

    public static function days($numberOfDays) {
        $endDate = CCarbon::today();
        $startDate = CCarbon::today()->subDays($numberOfDays)->startOfDay();
        return new static($startDate, $endDate);
    }

    public static function months($numberOfMonths) {
        $endDate = CCarbon::today();
        $startDate = CCarbon::today()->subMonths($numberOfMonths)->startOfDay();
        return new static($startDate, $endDate);
    }

    public static function years($numberOfYears) {
        $endDate = CCarbon::today();
        $startDate = CCarbon::today()->subYears($numberOfYears)->startOfDay();
        return new static($startDate, $endDate);
    }

    public static function minutes($numberOfMinutes) {
        $endDate = CCarbon::now();
        $startDate = CCarbon::now()->subMinutes($numberOfMinutes);
        return new static($startDate, $endDate);
    }

    public function __construct(DateTime $startDate, DateTime $endDate) {
        if ($startDate > $endDate) {
            throw CPeriod_Exception_InvalidPeriod::startDateCannotBeAfterEndDate($startDate, $endDate);
        }
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

}
