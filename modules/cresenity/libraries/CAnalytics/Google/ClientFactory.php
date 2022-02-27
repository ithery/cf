<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Cache\Adapter\Psr16Adapter;

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CAnalytics
 * @since Jun 23, 2019, 12:48:42 PM
 */

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;

class CAnalytics_Google_ClientFactory {
    /**
     * @param array $analyticsConfig
     *
     * @return CAnalytics_Google_ClientGA4
     */
    public static function createForGA4Config(array $analyticsConfig) {
        static::validateG4Config($analyticsConfig);
        $config = c::collect($analyticsConfig);
        $clientConfig = [];

        $clientConfig['credentials'] = $config['service_account_credentials_json'];

        $client = new BetaAnalyticsDataClient($clientConfig);

        $clientGA4 = new CAnalytics_Google_ClientGA4($client, CCache::store($config->get('store')));

        $clientGA4->setCacheLifeTimeInMinutes($config['cache_lifetime_in_minutes']);

        return $clientGA4;
    }

    /**
     * @param array $analyticsConfig
     *
     * @return CAnalytics_Google_Client
     */
    public static function createForConfig(array $analyticsConfig) {
        static::validateConfig($analyticsConfig);

        $authenticatedClient = self::createAuthenticatedGoogleClient($analyticsConfig);
        $googleService = new Google_Service_Analytics($authenticatedClient);

        return self::createAnalyticsClient($analyticsConfig, $googleService);
    }

    /**
     * @param array $config
     *
     * @return Google_Client
     */
    public static function createAuthenticatedGoogleClient(array $config) {
        $client = new Google_Client();
        $client->setScopes([
            Google_Service_Analytics::ANALYTICS_READONLY,
        ]);
        $client->setAuthConfig($config['service_account_credentials_json']);

        return $client;
    }

    protected static function configureCache(Google_Client $client, $config) {
        $config = c::collect($config);
        $store = \CCache::store($config->get('store'));
        $cache = new Psr16Adapter($store);
        $client->setCache($cache);
        $client->setCacheConfig(
            $config->except('store')->toArray()
        );
    }

    protected static function createAnalyticsClient(array $analyticsConfig, Google_Service_Analytics $googleService) {
        $client = new CAnalytics_Google_Client($googleService, CCache::repository());
        $client->setCacheLifeTimeInMinutes($analyticsConfig['cache_lifetime_in_minutes']);

        return $client;
    }

    public static function validateConfig(array $analyticsConfig) {
        if (empty($analyticsConfig['view_id'])) {
            throw CAnalytics_Google_Exception_InvalidConfigurationException::viewIdNotSpecified();
        }

        if (is_array($analyticsConfig['service_account_credentials_json'])) {
            return;
        }

        if (!file_exists($analyticsConfig['service_account_credentials_json'])) {
            throw CAnalytics_Google_Exception_InvalidConfigurationException::credentialsJsonDoesNotExist($analyticsConfig['service_account_credentials_json']);
        }
    }

    public static function validateG4Config(array $analyticsConfig) {
        if (empty($analyticsConfig['property_id'])) {
            throw CAnalytics_Google_Exception_InvalidConfigurationException::propertyIdNotSpecified();
        }

        if (is_array($analyticsConfig['service_account_credentials_json'])) {
            return;
        }
        if (!file_exists($analyticsConfig['service_account_credentials_json'])) {
            throw CAnalytics_Google_Exception_InvalidConfigurationException::credentialsJsonDoesNotExist($analyticsConfig['service_account_credentials_json']);
        }
    }
}
