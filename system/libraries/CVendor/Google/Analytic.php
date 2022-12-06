<?php

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;

class CVendor_Google_Analytic {
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
        $clientGA4->setCacheRealtimeLifeTimeInMinutes(carr::get($config, 'cache_realtime_lifetime_in_minutes', $config['cache_lifetime_in_minutes']));

        return $clientGA4;
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
