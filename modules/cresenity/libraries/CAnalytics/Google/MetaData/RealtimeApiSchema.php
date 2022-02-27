<?php

class CAnalytics_Google_MetaData_RealtimeApiSchema {
    /**
     * @return CAnalytics_Google_MetaData_RealtimeApiSchema_Dimensions
     */
    public static function dimension() {
        return CBase::forwarderStaticClass(CAnalytics_Google_MetaData_RealtimeApiSchema_Dimensions::class);
    }

    /**
     * @return CAnalytics_Google_MetaData_RealtimeApiSchema_Metrics
     */
    public static function metric() {
        return CBase::forwarderStaticClass(CAnalytics_Google_MetaData_RealtimeApiSchema_Metrics::class);
    }

    public static function dimensionList() {
        return [
            'appVersion',
            'audienceId',
            'audienceName',
            'city',
            'cityId',
            'country',
            'countryId',
            'deviceCategory',
            'eventName',
            'minutesAgo',
            'platform',
            'streamId',
            'streamName',
            'unifiedScreenName',
        ];
    }

    public static function metricList() {
        return [
            'activeUsers',
            'conversions',
            'eventCount',
            'screenPageViews',
        ];
    }
}
