<?php

class CPeriod_OpeningHours_PreciseTime extends CPeriod_OpeningHours_Time {
    /**
     * @var DateTimeInterface
     */
    protected $dateTime;

    protected function __construct(DateTimeInterface $dateTime) {
        $this->dateTime = $dateTime;
    }

    /**
     * @param string $string
     *
     * @return parent
     */
    public static function fromString($string) {
        return self::fromDateTime(new DateTimeImmutable($string));
    }

    /**
     * @return int
     */
    public function hours() {
        return (int) $this->dateTime->format('G');
    }

    public function minutes(): int {
        return (int) $this->dateTime->format('i');
    }

    public static function fromDateTime(DateTimeInterface $dateTime): parent {
        return new self($dateTime);
    }

    /**
     * @param CPeriod_OpeningHours_Time $time
     *
     * @return bool
     */
    public function isSame(CPeriod_OpeningHours_Time $time) {
        return $this->format('H:i:s.u') === $time->format('H:i:s.u');
    }

    /**
     * @param CPeriod_OpeningHours_Time $time
     *
     * @return bool
     */
    public function isAfter(CPeriod_OpeningHours_Time $time) {
        return $this->format('H:i:s.u') > $time->format('H:i:s.u');
    }

    /**
     * @param CPeriod_OpeningHours_Time $time
     *
     * @return bool
     */
    public function isBefore(CPeriod_OpeningHours_Time $time) {
        return $this->format('H:i:s.u') < $time->format('H:i:s.u');
    }

    /**
     * @param CPeriod_OpeningHours_Time $time
     *
     * @return bool
     */
    public function isSameOrAfter(CPeriod_OpeningHours_Time $time) {
        return $this->format('H:i:s.u') >= $time->format('H:i:s.u');
    }

    /**
     * @param CPeriod_OpeningHours_Time $time
     *
     * @return DateInterval
     */
    public function diff(CPeriod_OpeningHours_Time $time) {
        return $this->toDateTime()->diff($time->toDateTime());
    }

    public function toDateTime(DateTimeInterface $date = null): DateTimeInterface {
        return $date
            ? $this->copyDateTime($date)->modify($this->format('H:i:s.u'))
            : $this->copyDateTime($this->dateTime);
    }

    /**
     * @param string $format
     * @param mixed  $timezone
     *
     * @return string
     */
    public function format($format = 'H:i', $timezone = null) {
        $date = $timezone
            ? $this->copyDateTime($this->dateTime)->setTimezone(
                $timezone instanceof DateTimeZone
                ? $timezone
                : new DateTimeZone($timezone)
            )
            : $this->dateTime;

        return $date->format($format);
    }
}
