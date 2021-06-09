<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 24, 2019, 1:21:39 AM
 */
class CAnalytics {
    public static function createPeriod() {
        // return new CAnalytics_Period();
    }

    public static function google($options) {
        return new CAnalytics_Google($options);
    }
}
