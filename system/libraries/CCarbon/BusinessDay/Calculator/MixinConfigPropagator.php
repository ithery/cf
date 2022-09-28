<?php

final class CCarbon_BusinessDay_Calculator_MixinConfigPropagator {
    /**
     * @param CCarbon_BusinessCalendar $mixin
     * @param mixed                    $from
     * @param mixed                    $to
     *
     * @return void
     */
    public static function propagate(CCarbon_BusinessCalendar $mixin, $from, $to) {
        foreach ([
            $mixin->businessDayCheckers,
            $mixin->holidayGetters,
            $mixin->workdayGetters,
        ] as $config) {
            if ($config && isset($config[$from])) {
                $config[$to] = $config[$from];
            }
        }
    }

    /**
     * @param CCarbon_BusinessCalendar $mixin
     * @param mixed                    $date
     * @param string                   $method
     *
     * @return void
     */
    public static function apply(CCarbon_BusinessCalendar $mixin, $date, $method) {
        $result = $date->$method();

        if (!($date instanceof DateTime)) {
            self::propagate($mixin, $date, $result);
        }

        return $result;
    }

    public static function setBusinessDayChecker(CCarbon_BusinessCalendar $mixin, $date, ?callable $checkCallback) {
        return self::setStrategy('businessDayChecker', $mixin, $date, $checkCallback);
    }

    public static function getBusinessDayChecker(CCarbon_BusinessCalendar $mixin, $date): ?callable {
        return self::getStrategy('businessDayChecker', $mixin, $date);
    }

    public static function setHolidayGetter(CCarbon_BusinessCalendar $mixin, $date, ?callable $holidayGetter) {
        return self::setStrategy('holidayGetter', $mixin, $date, $holidayGetter);
    }

    public static function getHolidayGetter(CCarbon_BusinessCalendar $mixin, $date): ?callable {
        return self::getStrategy('holidayGetter', $mixin, $date);
    }

    public static function setExtraWorkdayGetter(CCarbon_BusinessCalendar $mixin, $date, ?callable $holidayGetter) {
        return self::setStrategy('workdayGetter', $mixin, $date, $holidayGetter);
    }

    public static function getExtraWorkdayGetter(CCarbon_BusinessCalendar $mixin, $date): ?callable {
        return self::getStrategy('workdayGetter', $mixin, $date);
    }

    /**
     * @param string                   $strategy
     * @param CCarbon_BusinessCalendar $mixin
     * @param mixed                    $date
     * @param null|callable            $callback
     *
     * @return mixed
     */
    private static function setStrategy(string $strategy, CCarbon_BusinessCalendar $mixin, $date, $callback) {
        $storage = $date ?: $mixin;

        if (!$date) {
            $storage->$strategy = $callback;

            return null;
        }

        // If mutable
        if ($date instanceof DateTime) {
            $date->$strategy = $callback;

            return $date;
        }

        $plural = $strategy . 's';

        if (!$mixin->$plural) {
            $mixin->$plural = new SplObjectStorage();
        }

        $mixin->$plural[$date] = $callback;

        return $date;
    }

    /**
     * @param string                   $strategy
     * @param CCarbon_BusinessCalendar $mixin
     * @param mixed                    $date
     *
     * @return null|callable
     */
    private static function getStrategy(string $strategy, CCarbon_BusinessCalendar $mixin, $date) {
        if ($date && isset($date->$strategy)) {
            return $date->$strategy;
        }

        $plural = $strategy . 's';

        return $mixin->$plural[$date] ?? $mixin->$strategy;
    }
}
