<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 24, 2019, 1:21:39 AM
 */
class CAnalytics {
    public static function google($options) {
        return CAnalytics_Google::universalAnalytic($options);
    }

    public static function googleGA4($options) {
        return  CAnalytics_Google::ga4Analytic($options);
    }
}
