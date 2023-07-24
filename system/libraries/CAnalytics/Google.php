<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CAnalytics
 */
class CAnalytics_Google {
    /**
     * @param array $options
     *
     * @return CAnalytics_Google_Analytic
     */
    public static function universalAnalytic($options) {
        $client = CAnalytics_Google_ClientFactory::createForConfig($options = []);

        return new CAnalytics_Google_Analytic($client, carr::get($options, 'view_id'));
    }

    /**
     * @param array $options
     *
     * @return CAnalytics_Google_AnalyticGA4
     */
    public static function ga4Analytic($options) {
        $client = CAnalytics_Google_ClientFactory::createForGA4Config($options);

        return new CAnalytics_Google_AnalyticGA4($client, carr::get($options, 'property_id'));
    }
}
