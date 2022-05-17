<?php

class CPeriod_Factory {
    /**
     * @param DateTimeInterface|string $start
     * @param DateTimeInterface|string $end
     * @param null|Precision           $precision
     * @param null|Boundaries          $boundaries
     * @param null|string              $format
     *
     * @return CPeriod
     */
    public static function make(
        $start,
        $end,
        $precision = null,
        $boundaries = null,
        $format = null
    ) {
        $boundaries = $boundaries ?: CPeriod_Boundaries::EXCLUDE_NONE();
        $precision = $precision ?: CPeriod_Precision::DAY();
        $start = $precision->roundDate(self::resolveDate($start, $format));
        $end = $precision->roundDate(self::resolveDate($end, $format));

        $period = new CPeriod($start, $end, $precision, $boundaries);

        return $period;
    }

    /**
     * @param DateTimeImmutable  $includedStart
     * @param DateTimeImmutable  $includedEnd
     * @param CPeriod_Precision  $precision
     * @param CPeriod_Boundaries $boundaries
     *
     * @return CPeriod
     */
    public static function makeWithBoundaries(
        $includedStart,
        $includedEnd,
        $precision,
        $boundaries
    ) {
        $includedStart = $precision->roundDate(self::resolveDate($includedStart));
        $includedEnd = $precision->roundDate(self::resolveDate($includedEnd));

        $period = new CPeriod(
            $boundaries->realStart($includedStart, $precision),
            $boundaries->realEnd($includedEnd, $precision),
            $precision,
            $boundaries,
        );

        return $period;
    }

    /**
     * @param DateTimeInterface|string $date
     * @param null|string              $format
     *
     * @return DateTimeImmutable
     */
    protected static function resolveDate(
        $date,
        $format = null
    ) {
        if ($date instanceof DateTimeImmutable) {
            return $date;
        }

        if ($date instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($date);
        }

        if (!is_string($date)) {
            throw CPeriod_Exception_InvalidDateException::forFormat($date, $format);
        }

        $format = static::resolveFormat($date, $format);

        $dateTime = DateTimeImmutable::createFromFormat($format, $date);

        if ($dateTime === false) {
            throw CPeriod_Exception_InvalidDateException::forFormat($date, $format);
        }

        if (!cstr::contains($format, ' ')) {
            $dateTime = $dateTime->setTime(0, 0, 0);
        }

        return $dateTime;
    }

    /**
     * @param string      $date
     * @param null|string $format
     *
     * @return string
     */
    protected static function resolveFormat(
        $date,
        $format
    ) {
        if ($format !== null) {
            return $format;
        }

        if (cstr::contains($date, ' ')) {
            return 'Y-m-d H:i:s';
        }

        return 'Y-m-d';
    }
}
