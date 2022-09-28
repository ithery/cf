<?php

class CPeriod_OpeningHours_Time {
    use CPeriod_Trait_DataTrait;
    use CPeriod_Trait_DateTimeCopierTrait;

    /**
     * @var int
     */
    protected $hours;

    /**
     * @var int
     */
    protected $minutes;

    /**
     * @param int $hours
     * @param int $minutes
     */
    protected function __construct($hours, $minutes) {
        $this->hours = $hours;
        $this->minutes = $minutes;
    }

    /**
     * @param string $string
     *
     * @return self
     */
    public static function fromString($string) {
        if (!preg_match('/^(([0-1][0-9]|2[0-3]):[0-5][0-9]|24:00)$/', $string)) {
            throw CPeriod_OpeningHours_Exception_InvalidTimeStringException::forString($string);
        }

        list($hours, $minutes) = explode(':', $string);

        return new self($hours, $minutes);
    }

    /**
     * @return int
     */
    public function hours() {
        return $this->hours;
    }

    /**
     * @return int
     */
    public function minutes() {
        return $this->minutes;
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return self
     */
    public static function fromDateTime(DateTimeInterface $dateTime) {
        return static::fromString($dateTime->format('H:i'));
    }

    /**
     * @param self $time
     *
     * @return bool
     */
    public function isSame(CPeriod_OpeningHours_Time $time) {
        return $this->hours === $time->hours && $this->minutes === $time->minutes;
    }

    /**
     * @param self $time
     *
     * @return bool
     */
    public function isAfter(CPeriod_OpeningHours_Time $time) {
        if ($this->isSame($time)) {
            return false;
        }

        if ($this->hours > $time->hours) {
            return true;
        }

        return $this->hours === $time->hours && $this->minutes >= $time->minutes;
    }

    /**
     * @param self $time
     *
     * @return bool
     */
    public function isBefore(CPeriod_OpeningHours_Time $time) {
        if ($this->isSame($time)) {
            return false;
        }

        return !$this->isAfter($time);
    }

    /**
     * @param self $time
     *
     * @return bool
     */
    public function isSameOrAfter(CPeriod_OpeningHours_Time $time) {
        return $this->isSame($time) || $this->isAfter($time);
    }

    /**
     * @param self $time
     *
     * @return DateInterval
     */
    public function diff(CPeriod_OpeningHours_Time $time) {
        return $this->toDateTime()->diff($time->toDateTime());
    }

    /**
     * @param null|DateTimeInterface $date
     *
     * @return DateTimeInterface
     */
    public function toDateTime(DateTimeInterface $date = null) {
        $date = $date ? $this->copyDateTime($date) : new DateTime('1970-01-01 00:00:00');

        return $date->setTime($this->hours, $this->minutes);
    }

    /**
     * @param string $format
     * @param mixed  $timezone
     *
     * @return string
     */
    public function format($format = 'H:i', $timezone = null) {
        $date = $timezone
            ? new DateTime(
                '1970-01-01 00:00:00',
                $timezone instanceof DateTimeZone
                ? $timezone
                : new DateTimeZone($timezone)
            )
            : null;

        if ($this->hours === 24 && $this->minutes === 0 && substr($format, 0, 3) === 'H:i') {
            return '24:00' . (
                strlen($format) > 3
                    ? ($date ?? new DateTimeImmutable('1970-01-01 00:00:00'))->format(substr($format, 3))
                    : ''
            );
        }

        return $this->toDateTime($date)->format($format);
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->format();
    }
}
