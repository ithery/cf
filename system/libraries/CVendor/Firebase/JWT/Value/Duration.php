<?php

/**
 * Adapted duration class from gamez/duration.
 *
 * @see https://github.com/jeromegamez/duration-php
 */
final class CVendor_Firebase_JWT_Value_Duration {
    public const NONE = 'PT0S';

    private DateInterval $value;

    private function __construct(DateInterval $value) {
        $this->value = $value;
    }

    /**
     * @param self|DateInterval|int|string $value
     *
     * @return self
     */
    public static function make($value) {
        if ($value instanceof self) {
            return $value;
        }

        if ($value instanceof DateInterval) {
            return self::fromDateInterval($value);
        }

        if (\is_int($value)) {
            return self::inSeconds($value);
        }

        if (\mb_strpos($value, 'P') === 0) {
            return self::fromDateIntervalSpec($value);
        }

        try {
            $interval = DateInterval::createFromDateString($value);
        } catch (Throwable $e) {
            throw new InvalidArgumentException("Unable to determine a duration from '{$value}'");
        }

        $duration = self::fromDateInterval($interval);

        // If the string doesn't contain a zero, but the result equals to zero
        // the value must be invalid.
        if (\mb_strpos($value, '0') === false && $duration->equals(self::none())) {
            throw new InvalidArgumentException("Unable to determine a duration from '{$value}'");
        }

        return $duration;
    }

    /**
     * @param mixed $seconds
     */

    /**
     * @param int $seconds
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public static function inSeconds($seconds) {
        if ($seconds < 0) {
            throw new InvalidArgumentException('A duration can not be negative');
        }

        return self::fromDateIntervalSpec('PT' . $seconds . 'S');
    }

    /**
     * @param string $spec
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public static function fromDateIntervalSpec($spec) {
        try {
            $interval = new DateInterval($spec);
        } catch (Throwable $e) {
            throw new InvalidArgumentException("'{$spec}' is not a valid DateInterval specification");
        }

        return self::fromDateInterval($interval);
    }

    /**
     * @param DateInterval $interval
     *
     * @return self
     */
    public static function fromDateInterval(DateInterval $interval) {
        $now = new DateTimeImmutable();
        $then = $now->add($interval);

        if ($then < $now) {
            throw new InvalidArgumentException('A duration can not be negative');
        }

        return new self($interval);
    }

    /**
     * @return self
     */
    public static function none() {
        return self::fromDateIntervalSpec(self::NONE);
    }

    /**
     * @return DateInterval
     */
    public function value() {
        return $this->value;
    }

    /**
     * @param self|DateInterval|int|string $other
     *
     * @return bool
     */
    public function isLargerThan($other) {
        return 1 === $this->compareTo($other);
    }

    /**
     * @param self|DateInterval|int|string $other
     *
     * @return bool
     */
    public function equals($other) {
        return 0 === $this->compareTo($other);
    }

    /**
     * @param self|DateInterval|int|string $other
     *
     * @return bool
     */
    public function isSmallerThan($other) {
        return -1 === $this->compareTo($other);
    }

    /**
     * @param self|DateInterval|int|string $other
     *
     * @return int
     */
    public function compareTo($other) {
        $other = self::make($other);

        $now = self::now();

        return $now->add($this->value) <=> $now->add($other->value);
    }

    /**
     * @return string
     */
    public function toString() {
        return self::toDateIntervalSpec(self::normalizeInterval($this->value));
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * @return DateTimeImmutable
     */
    private static function now() {
        return new DateTimeImmutable('@' . \time());
    }

    /**
     * @param DateInterval $value
     *
     * @return DateInterval
     */
    private static function normalizeInterval(DateInterval $value) {
        $now = self::now();
        $then = $now->add($value);

        return $now->diff($then);
    }

    /**
     * @param DateInterval $value
     *
     * @return string
     */
    private static function toDateIntervalSpec(DateInterval $value) {
        $spec = 'P';
        $spec .= 0 !== $value->y ? $value->y . 'Y' : '';
        $spec .= 0 !== $value->m ? $value->m . 'M' : '';
        $spec .= 0 !== $value->d ? $value->d . 'D' : '';

        $spec .= 'T';
        $spec .= 0 !== $value->h ? $value->h . 'H' : '';
        $spec .= 0 !== $value->i ? $value->i . 'M' : '';
        $spec .= 0 !== $value->s ? $value->s . 'S' : '';

        if ('T' === \mb_substr($spec, -1)) {
            $spec = \mb_substr($spec, 0, -1);
        }

        if ('P' === $spec) {
            return self::NONE;
        }

        return $spec;
    }
}
