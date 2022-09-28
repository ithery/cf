<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 12:50:57 PM
 */
use Carbon\Carbon;

class CPeriod implements IteratorAggregate {
    use CPeriod_Trait_FactoryTrait;
    use CPeriod_Trait_OperationTrait;
    use CPeriod_Trait_ComparisonTrait;
    use CPeriod_Trait_GetterTrait;

    const INTERVAL_MONTH = 'month';

    const INTERVAL_DAY = 'day';

    const INTERVAL_HOUR = 'hour';

    /**
     * @var \CarbonV3\Carbon
     */
    public $startDate;

    /**
     * @var \CarbonV3\Carbon
     */
    public $endDate;

    /**
     * @var DateInterval
     */
    protected $interval;

    /**
     * @var CPeriod_Precision
     */
    protected $precision;

    /**
     * @var CPeriod_Boundaries
     */
    protected $boundaries;

    /**
     * @var CPeriod_Duration
     */
    protected $duration;

    /**
     * @var DateTimeImmutable
     */
    protected $includedStart;

    /**
     * @var DateTimeImmutable
     */
    protected $includedEnd;

    public function __construct($startDate, $endDate, CPeriod_Precision $precision = null, CPeriod_Boundaries $boundaries = null) {
        if ($startDate > $endDate) {
            throw CPeriod_Exception_InvalidPeriodException::startDateCannotBeAfterEndDate($startDate, $endDate);
        }
        if ($startDate instanceof DateTime) {
            $startDate = new Carbon($startDate->format('Y-m-d H:i:s.u'), $startDate->getTimezone());
        }
        if ($endDate instanceof DateTime) {
            $endDate = new Carbon($endDate->format('Y-m-d H:i:s.u'), $endDate->getTimezone());
        }
        if ($precision == null) {
            $precision = CPeriod_Precision::DAY();
        }
        if ($boundaries == null) {
            $boundaries = CPeriod_Boundaries::EXCLUDE_NONE();
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->precision = $precision;
        $this->boundaries = $boundaries;
        $this->interval = $this->precision->interval();
        $this->includedStart = $boundaries->startIncluded() ? $startDate : $startDate->add($this->interval);
        $this->includedEnd = $boundaries->endIncluded() ? $endDate : $endDate->sub($this->interval);
        $this->duration = new CPeriod_Duration($this);
    }

    public static function create($startDate, $endDate) {
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

    public static function today() {
        $endDate = CCarbon::today()->endOfDay();
        $startDate = CCarbon::today()->startOfDay();

        return new static($startDate, $endDate);
    }

    public static function yesterday() {
        $endDate = CCarbon::today()->subDays(1)->endOfDay();
        $startDate = CCarbon::today()->subDays(1)->startOfDay();

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

    public static function thisWeek() {
        $startDate = CCarbon::now()->modify('this week');
        $endDate = CCarbon::now()->modify('this week +6 days');

        $startDate->hour = 0;
        $startDate->minute = 0;
        $startDate->second = 0;

        $endDate->hour = 23;
        $endDate->minute = 59;
        $endDate->second = 59;

        return new static($startDate, $endDate);
    }

    public static function lastWeek() {
        $startDate = CCarbon::now()->modify('last week');
        $endDate = CCarbon::now()->modify('last week +6 days');

        $startDate->hour = 0;
        $startDate->minute = 0;
        $startDate->second = 0;

        $endDate->hour = 23;
        $endDate->minute = 59;
        $endDate->second = 59;

        return new static($startDate, $endDate);
    }

    public static function thisMonth() {
        $startDate = CCarbon::now()->modify('first day of this month');
        $endDate = CCarbon::now()->modify('last day of this month');

        $startDate->hour = 0;
        $startDate->minute = 0;
        $startDate->second = 0;

        $endDate->hour = 23;
        $endDate->minute = 59;
        $endDate->second = 59;

        return new static($startDate, $endDate);
    }

    public static function thisYear() {
        $startDate = CCarbon::now()->modify('first day of this year');
        $endDate = CCarbon::now()->modify('last day of this year');

        $startDate->hour = 0;
        $startDate->minute = 0;
        $startDate->second = 0;

        $endDate->hour = 23;
        $endDate->minute = 59;
        $endDate->second = 59;

        return new static($startDate, $endDate);
    }

    public static function untilDateNow() {
        $startDate = CCarbon::createFromTimestamp(0);
        $endDate = CCarbon::now();
        $startDate->hour = 0;
        $startDate->minute = 0;
        $startDate->second = 0;

        $endDate->hour = 23;
        $endDate->minute = 59;
        $endDate->second = 59;

        return new static($startDate, $endDate);
    }

    public static function createFromInterval($interval = 'month', $count = 1, $start = '') {
        if (empty($start)) {
            $start = CCarbon::now();
        } elseif (!$start instanceof Carbon) {
            $start = new CCarbon($start);
        } else {
            $start = $start;
        }

        $startCloned = clone $start;
        $method = 'add' . ucfirst($interval) . 's';
        $end = $startCloned->{$method}($count);
        if (strtolower($interval) == 'month') {
            if ($start->format('d') != $end->format('d')) {
                if ((int) $end->format('d') === 1) {
                    $end = $end->subMonths(1)->endOfMonth();
                }
            }
        }

        return new static($start, $end);
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getStartDate() {
        return $this->startDate;
    }

    public function toArray() {
        return [$this->startDate, $this->endDate];
    }

    /**
     * @return DatePeriod
     */
    public function getIterator() {
        return new DatePeriod(
            $this->includedStart(),
            $this->interval,
            // We need to add 1 second (the smallest unit available within this package) to ensure entries are counted correctly
            $this->includedEnd()->add(new DateInterval('PT1S'))
        );
    }

    /**
     * @param CPeriod $other
     *
     * @return void
     */
    protected function ensurePrecisionMatches(CPeriod $other) {
        if ($this->precision->equals($other->precision)) {
            return;
        }

        throw CPeriod_Exception_CannotComparePeriodException::precisionDoesNotMatch();
    }
}
