<?php

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CConsole {
    const SUCCESS_EXIT = 0;

    const FAILURE_EXIT = 1;

    const EXCEPTION_EXIT = 2;

    private $schedule;

    public static function domain() {
        return CF::cliDomain();
    }

    public static function prefix() {
        $appConfig = CConfig::instance('app');
        $appConfig->refresh();

        return $appConfig->get('prefix');
    }

    public static function domainRequired($console) {
        $domain = CConsole::domain();
        if (strlen($domain) == 0) {
            $console->error('Domain is not set');
            $console->error('Please set your domain using domain:switch command');
            exit(static::FAILURE_EXIT);
        }
        if (!CF::domainExists($domain)) {
            $console->error('Domain ' . $domain . ' is not exists on data file');
            $console->error('Please recheck your domain or create the domain using domain:create command');
            exit(static::FAILURE_EXIT);
        }
    }

    public static function devSuiteRequired($console) {
        if (!CDevSuite::isInstalled()) {
            $console->error('devsuite is not installed');
            $console->error('Please install devsuite using devsuite:install command');
            exit(static::FAILURE_EXIT);
        }
    }

    public static function phpUnitRequired($console) {
        if (!CTesting::isInstalled()) {
            $console->error('testing is not installed');
            $console->error('Please install testing using test:install command');
            exit(static::FAILURE_EXIT);
        }
    }

    public static function prefixRequired($console) {
        static::domainRequired($console);

        if (strlen(static::prefix()) == 0) {
            $console->error('Prefix is not set');
            $console->error('Please set your prefix in config app.php');
            exit(static::FAILURE_EXIT);
        }
    }

    /**
     * Get CConsole Schedule Object.
     *
     * @return CConsole_Schedule
     */
    public static function schedule() {
        if (static::$schedule == null) {
            static::$schedule = c::tap(new CConsole_Schedule(static::scheduleTimezone()), function (CConsole_Schedule $schedule) {
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
        return CF::config('app.schedule_timezone', CF::config('app.timezone'));
    }

    /**
     * Get the name of the cache store that should manage scheduling mutexes.
     *
     * @return string
     */
    protected static function scheduleCache() {
        return CF::config('cache.schedule_store', CEnv::get('SCHEDULE_CACHE_DRIVER'));
    }
}
