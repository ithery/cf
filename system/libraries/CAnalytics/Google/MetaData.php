<?php
class CAnalytics_Google_MetaData {
    /**
     * @return CAnalytics_Google_MetaData_ApiSchema
     */
    public static function schema() {
        return CBase::forwarderStaticClass(CAnalytics_Google_MetaData_ApiSchema::class);
    }

    /**
     * @return CAnalytics_Google_MetaData_RealtimeApiSchema
     */
    public static function realtimeSchema() {
        return CBase::forwarderStaticClass(CAnalytics_Google_MetaData_RealtimeApiSchema::class);
    }
}
