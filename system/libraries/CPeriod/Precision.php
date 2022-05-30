<?php

class CPeriod_Precision {
    const YEAR = 0b100000;

    const MONTH = 0b110000;

    const DAY = 0b111000;

    const HOUR = 0b111100;

    const MINUTE = 0b111110;

    const SECOND = 0b111111;

    /**
     * @var int
     */
    private $mask;

    /**
     * @return self[]
     */
    public static function all() {
        return [
            self::YEAR(),
            self::MONTH(),
            self::DAY(),
            self::HOUR(),
            self::MINUTE(),
            self::SECOND(),
        ];
    }

    private function __construct($mask) {
        $this->mask = $mask;
    }

    /**
     * @param string $string
     *
     * @return self
     */
    public static function fromString($string) {
        preg_match('/([\d]{4})(-[\d]{2})?(-[\d]{2})?(\s[\d]{2})?(:[\d]{2})?(:[\d]{2})?/', $string, $matches);

        $matchMap = [
            1 => self::YEAR(),
            2 => self::MONTH(),
            3 => self::DAY(),
            4 => self::HOUR(),
            5 => self::MINUTE(),
            6 => self::SECOND(),

        ];
        $match = carr::get($matchMap, count($matches) - 1);

        return $match;
    }

    /**
     * @return self
     */
    public static function YEAR() {
        return new self(self::YEAR);
    }

    /**
     * @return self
     */
    public static function MONTH() {
        return new self(self::MONTH);
    }

    /**
     * @return self
     */
    public static function DAY() {
        return new self(self::DAY);
    }

    /**
     * @return self
     */
    public static function HOUR() {
        return new self(self::HOUR);
    }

    /**
     * @return self
     */
    public static function MINUTE() {
        return new self(self::MINUTE);
    }

    /**
     * @return self
     */
    public static function SECOND() {
        return new self(self::SECOND);
    }

    /**
     * @return DateInterval
     */
    public function interval() {
        $intervalMap = [
            self::SECOND => 'PT1S',
            self::MINUTE => 'PT1M',
            self::HOUR => 'PT1H',
            self::DAY => 'P1D',
            self::MONTH => 'P1M',
            self::YEAR => 'P1Y',
        ];
        $interval = carr::get($intervalMap, $this->mask);

        return new DateInterval($interval);
    }

    /**
     * @return string
     */
    public function intervalName() {
        $matchMap = [
            self::YEAR => 'y',
            self::MONTH => 'm',
            self::DAY => 'd',
            self::HOUR => 'h',
            self::MINUTE => 'i',
            self::SECOND => 's',
        ];

        return carr::get($matchMap, $this->mask);
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return DateTimeImmutable
     */
    public function roundDate(DateTimeInterface $date): DateTimeImmutable {
        list($year, $month, $day, $hour, $minute, $second) = explode(' ', $date->format('Y m d H i s'));

        $month = (self::MONTH & $this->mask) === self::MONTH ? $month : '01';
        $day = (self::DAY & $this->mask) === self::DAY ? $day : '01';
        $hour = (self::HOUR & $this->mask) === self::HOUR ? $hour : '00';
        $minute = (self::MINUTE & $this->mask) === self::MINUTE ? $minute : '00';
        $second = (self::SECOND & $this->mask) === self::SECOND ? $second : '00';

        return DateTimeImmutable::createFromFormat(
            'Y m d H i s',
            implode(' ', [$year, $month, $day, $hour, $minute, $second]),
            $date->getTimezone()
        );
    }

    /**
     * @param DateTimeInterface $date
     * @param CPeriod_Precision $precision
     *
     * @return DateTimeImmutable
     */
    public function ceilDate(DateTimeInterface $date, CPeriod_Precision $precision) {
        list($year, $month, $day, $hour, $minute, $second) = explode(' ', $date->format('Y m d H i s'));

        $month = (self::MONTH & $precision->mask) === self::MONTH ? $month : '12';
        $day = (self::DAY & $precision->mask) === self::DAY ? $day : cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $hour = (self::HOUR & $precision->mask) === self::HOUR ? $hour : '23';
        $minute = (self::MINUTE & $precision->mask) === self::MINUTE ? $minute : '59';
        $second = (self::SECOND & $precision->mask) === self::SECOND ? $second : '59';

        return DateTimeImmutable::createFromFormat(
            'Y m d H i s',
            implode(' ', [$year, $month, $day, $hour, $minute, $second]),
            $date->getTimezone()
        );
    }

    /**
     * @param CPeriod_Precision ...$others
     *
     * @return bool
     */
    public function equals(CPeriod_Precision ...$others) {
        foreach ($others as $other) {
            if ($this->mask !== $other->mask) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @param DateTimeImmutable $date
     *
     * @return DateTimeImmutable
     */
    public function increment(DateTimeImmutable $date) {
        return $this->roundDate($date->add($this->interval()));
    }

    /**
     * @param DateTimeImmutable $date
     *
     * @return DateTimeImmutable
     */
    public function decrement(DateTimeImmutable $date) {
        return $this->roundDate($date->sub($this->interval()));
    }

    /**
     * @param CPeriod_Precision $other
     *
     * @return bool
     */
    public function higherThan(CPeriod_Precision $other) {
        return strlen($this->dateFormat()) > strlen($other->dateFormat());
    }

    /**
     * @return string
     */
    public function dateFormat() {
        $matchMap = [
            CPeriod_Precision::SECOND => 'Y-m-d H:i:s',
            CPeriod_Precision::MINUTE => 'Y-m-d H:i',
            CPeriod_Precision::HOUR => 'Y-m-d H',
            CPeriod_Precision::DAY => 'Y-m-d',
            CPeriod_Precision::MONTH => 'Y-m',
            CPeriod_Precision::YEAR => 'Y',
        ];

        return carr::get($matchMap, $this->mask);
    }
}
