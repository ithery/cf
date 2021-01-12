<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 12:48:42 PM
 */
class CAnalytics_Google_ClientFactory {
    public static function createForConfig(array $analyticsConfig) {
        $authenticatedClient = self::createAuthenticatedGoogleClient($analyticsConfig);
        $googleService = new Google_Service_Analytics($authenticatedClient);
        return self::createAnalyticsClient($analyticsConfig, $googleService);
    }

    public static function createAuthenticatedGoogleClient(array $config) {
        $client = new Google_Client();
        $client->setScopes([
            Google_Service_Analytics::ANALYTICS_READONLY,
        ]);
        $client->setAuthConfig($config['serviceAccountCredentialsJson']);
        //self::configureCache($client, $config['cache']);
        return $client;
    }

    protected static function configureCache(Google_Client $client, $config) {
        $config = c::collect($config);
        $store = \Cache::store($config->get('store'));
        $cache = new CacheItemPool($store);
        $client->setCache($cache);
        $client->setCacheConfig(
            $config->except('store')->toArray()
        );
    }

    protected static function createAnalyticsClient(array $analyticsConfig, Google_Service_Analytics $googleService) {
        $client = new CAnalytics_Google_Client($googleService);
        $client->setCacheLifeTimeInMinutes($analyticsConfig['cache_lifetime_in_minutes']);
        return $client;
    }
}
