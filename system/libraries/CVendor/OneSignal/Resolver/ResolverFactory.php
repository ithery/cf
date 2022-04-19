<?php

class CVendor_OneSignal_Resolver_ResolverFactory {
    private $config;

    public function __construct(CVendor_OneSignal_Config $config) {
        $this->config = $config;
    }

    /**
     * @return CVendor_OneSignal_Resolver_AppResolver
     */
    public function createAppResolver() {
        return new CVendor_OneSignal_Resolver_AppResolver();
    }

    /**
     * @return CVendor_OneSignal_Resolver_SegmentResolver
     */
    public function createSegmentResolver() {
        return new CVendor_OneSignal_Resolver_SegmentResolver();
    }

    /**
     * @return CVendor_OneSignal_Resolver_AppOutcomesResolver
     */
    public function createOutcomesResolver() {
        return new CVendor_OneSignal_Resolver_AppOutcomesResolver();
    }

    /**
     * @return CVendor_OneSignal_Resolver_DeviceSessionResolver
     */
    public function createDeviceSessionResolver() {
        return new CVendor_OneSignal_Resolver_DeviceSessionResolver();
    }

    /**
     * @return CVendor_OneSignal_Resolver_DevicePurchaseResolver
     */
    public function createDevicePurchaseResolver() {
        return new CVendor_OneSignal_Resolver_DevicePurchaseResolver();
    }

    /**
     * @return CVendor_OneSignal_Resolver_DeviceFocusResolver
     */
    public function createDeviceFocusResolver() {
        return new CVendor_OneSignal_Resolver_DeviceFocusResolver();
    }

    /**
     * @return CVendor_OneSignal_Resolver_DeviceResolver
     */
    public function createNewDeviceResolver() {
        return new CVendor_OneSignal_Resolver_DeviceResolver($this->config, true);
    }

    /**
     * @return CVendor_OneSignal_Resolver_DeviceResolver
     */
    public function createExistingDeviceResolver() {
        return new CVendor_OneSignal_Resolver_DeviceResolver($this->config, false);
    }

    /**
     * @return CVendor_OneSignal_Resolver_DeviceTagsResolver
     */
    public function createDeviceTagsResolver() {
        return new CVendor_OneSignal_Resolver_DeviceTagsResolver();
    }

    /**
     * @return CVendor_OneSignal_Resolver_NotificationResolver
     */
    public function createNotificationResolver() {
        return new CVendor_OneSignal_Resolver_NotificationResolver($this->config);
    }

    /**
     * @return CVendor_OneSignal_Resolver_NotificationHistoryResolver
     */
    public function createNotificationHistoryResolver() {
        return new CVendor_OneSignal_Resolver_NotificationHistoryResolver($this->config);
    }
}
