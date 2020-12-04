<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2019, 6:58:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRouting {

    /**
     * 
     * @return CRouting_UrlGenerator
     */
    public static function urlGenerator() {
        return CRouting_UrlGenerator::instance();
    }

}
