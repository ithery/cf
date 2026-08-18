<?php

defined('SYSPATH') or die('No direct access allowed.');


class CAnalytics {
    /**
     * @param array $options
     *
     * @return CAnalytics_Google_Analytic
     */
    public static function google($options) {
        return CAnalytics_Google::universalAnalytic($options);
    }

    /**
     * @param array $options
     *
     * @return CAnalytics_Google_AnalyticGA4
     */
    public static function googleGA4($options) {
        return  CAnalytics_Google::ga4Analytic($options);
    }
}
