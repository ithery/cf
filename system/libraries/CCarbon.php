<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
use Carbon\Carbon as BaseCarbon;

class CCarbon extends BaseCarbon implements JsonSerializable {
    /**
     * The custom Carbon JSON serializer.
     *
     * @var callable|null
     */
    protected static $serializer;

    /**
     * Prepare the object for JSON serialization.
     *
     * @return array|string
     */
    public function jsonSerialize() {
        if (static::$serializer) {
            return call_user_func(static::$serializer, $this);
        }

        $carbon = $this;

        return call_user_func(function () use ($carbon) {
            return get_object_vars($carbon);
        });
    }

    /**
     * JSON serialize all Carbon instances using the given callback.
     *
     * @param callable $callback
     *
     * @return void
     */
    public static function serializeUsing($callback) {
        static::$serializer = $callback;
    }

    /**
     * Get the difference in seconds using timestamps.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return int
     */
    public function diffInRealSeconds($date = null, $absolute = true) {
        /** @var Carbon\CarbonInterface $date */
        $date = $this->resolveCarbon($date);
        $value = $date->getTimestamp() - $this->getTimestamp();

        return $absolute ? abs($value) : $value;
    }

    /**
     * Return the Carbon instance passed through, a now instance in the same timezone
     * if null given or parse the input if string given.
     *
     * @param \Carbon\Carbon|\DateTimeInterface|string|null $date
     *
     * @return static
     */
    protected function resolveCarbon($date = null) {
        if (!$date) {
            return $this->nowWithSameTz();
        }

        if (is_string($date)) {
            return static::parse($date, $this->getTimezone());
        }

        static::expectDateTime($date, ['null', 'string']);

        return $date instanceof self ? $date : static::instance($date);
    }

    /**
     * Throws an exception if the given object is not a DateTime and does not implement DateTimeInterface.
     *
     * @param mixed        $date
     * @param string|array $other
     *
     * @throws \InvalidArgumentException
     */
    protected static function expectDateTime($date, $other = []) {
        $message = 'Expected ';
        foreach ((array) $other as $expect) {
            $message .= "$expect, ";
        }

        if (!$date instanceof DateTime && !$date instanceof DateTimeInterface) {
            throw new InvalidArgumentException(
                $message . 'DateTime or DateTimeInterface, '
                . (is_object($date) ? get_class($date) : gettype($date)) . ' given'
            );
        }
    }
}
