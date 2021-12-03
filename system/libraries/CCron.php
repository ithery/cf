<?php

class CCron {

    /**
     * @var CCron_Schedule
     */
    private static $schedule;


    /**
     * Get CConsole Schedule Object.
     *
     * @return CCron_Schedule
     */
    public static function schedule() {
        if (static::$schedule == null) {
            static::$schedule = c::tap(new CCron_Schedule(static::scheduleTimezone()), function (CCron_Schedule $schedule) {
                return $schedule->useCache(static::scheduleCache());
            });
        }

        return static::$schedule;
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return null|\DateTimeZone|string
     */
    protected static function scheduleTimezone() {
        return CF::config('app.cron_timezone', CF::config('app.timezone'));
    }

    /**
     * Get the name of the cache store that should manage scheduling mutexes.
     *
     * @return string
     */
    protected static function scheduleCache() {
        return CF::config('cron.cache.store', CEnv::get('CRON_CACHE_DRIVER'));
    }
}
