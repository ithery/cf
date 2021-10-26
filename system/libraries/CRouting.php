<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2019, 6:58:10 PM
 */
class CRouting {
    /**
     * @return CRouting_UrlGenerator
     */
    public static function urlGenerator() {
        return CRouting_UrlGenerator::instance();
    }

    /**
     * @return CRouting_Router
     */
    public static function router() {
        return CRouting_Router::instance();
    }

    /**
     * @return CRouting_Factory
     */
    public static function factory() {
        return CRouting_Factory::instance();
    }
}
