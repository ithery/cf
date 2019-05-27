<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 24, 2019, 1:23:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Carbon\Carbon;

class Period {

    /** @var \DateTime */
    public $startDate;

    /** @var \DateTime */
    public $endDate;

    public static function create(DateTime $startDate, $endDate) {
        return new static($startDate, $endDate);
    }

    public static function days($numberOfDays) {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($numberOfDays)->startOfDay();
        return new static($startDate, $endDate);
    }

    public static function months($numberOfMonths) {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subMonths($numberOfMonths)->startOfDay();
        return new static($startDate, $endDate);
    }

    public static function years($numberOfYears) {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subYears($numberOfYears)->startOfDay();
        return new static($startDate, $endDate);
    }

    public function __construct(DateTime $startDate, DateTime $endDate) {
        if ($startDate > $endDate) {
            throw CAnalytics_Exception_InvalidPeriodException::startDateCannotBeAfterEndDate($startDate, $endDate);
        }
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

}
