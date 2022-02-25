<?php

class CVendor_OneSignal_Resolver_ResolverFactory {

    private $config;

    public function __construct(CVendor_OneSignal_Config $config) {
        $this->config = $config;
    }

    public function createAppResolver() {
        return new CVendor_OneSignal_Resolver_AppResolver();
    }

    public function createDeviceSessionResolver() {
        return new CVendor_OneSignal_Resolver_DeviceSessionResolver();
    }

    public function createDevicePurchaseResolver() {
        return new CVendor_OneSignal_Resolver_DevicePurchaseResolver();
    }

    public function createDeviceFocusResolver() {
        return new CVendor_OneSignal_Resolver_DeviceFocusResolver();
    }

    public function createNewDeviceResolver() {
        return new CVendor_OneSignal_Resolver_DeviceResolver($this->config, true);
    }

    public function createExistingDeviceResolver() {
        return new CVendor_OneSignal_Resolver_DeviceResolver($this->config, false);
    }

    public function createNotificationResolver() {
        return new CVendor_OneSignal_Resolver_NotificationResolver($this->config);
    }

}
