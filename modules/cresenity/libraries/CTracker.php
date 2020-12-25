<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 1:07:59 AM
 */
class CTracker {
    /**
     * @var CTracker_Bootstrap
     */
    protected static $bootstrap;
    protected static $isBooted = false;

    public static function visitor() {
        return CTracker_RepositoryManager::instance()->sessionRepository->getCurrent();
    }

    public static function boot() {
        if (PHP_SAPI !== 'cli') {
            if (!self::$isBooted) {
                self::$bootstrap = new CTracker_Bootstrap();
                self::$bootstrap->execute();
                self::$isBooted = true;
            }
        }
    }

    /**
     * @return CTracker_Config
     */
    public static function config() {
        return CTracker_Config::instance();
    }

    public static function onlineUsers($minutes = 3, $results = true) {
        return self::sessions($minutes);
    }

    public static function sessions($minutes = 1440, $results = true) {
        return CTracker_RepositoryManager::instance()->getLastSessions(CPeriod::minutes($minutes), $results);
    }

    public function pageViews($minutes, $results = true) {
        return CTracker_RepositoryManager::instance()->pageViews(CPeriod::minutes($minutes), $results);
    }

    public function pageViewsByCountry($minutes, $results = true) {
        return CTracker_RepositoryManager::instance()->pageViewsByCountry(CPeriod::minutes($minutes), $results);
    }

    public function userDevices($minutes, $user_id = null, $results = true) {
        return CTracker_RepositoryManager::instance()->userDevices(CPeriod::minutes($minutes), $user_id, $results);
    }

    /**
     * @return CTracker_Populator
     */
    public static function populator() {
        return CTracker_Populator::instance();
    }

    /**
     * Check CTracker already booted
     *
     * @return type
     */
    public static function isBooted() {
        return static::$isBooted;
    }
}
