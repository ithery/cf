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
        CAnalytics_Google_ClientFactory::validateConfig($options);
        $client = CAnalytics_Google_ClientFactory::createForConfig($options);

        return new CAnalytics_Google($client, carr::get($options, 'view_id'));
    }
}
