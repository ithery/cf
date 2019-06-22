<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:07:59 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker {

    public static function visitor() {
        return CTracker_RepositoryManager::instance()->sessionRepository->getCurrent();
    }

    public static function boot() {
        $bootstrap = new CTracker_Bootstrap();
        $bootstrap->execute();
    }

}
