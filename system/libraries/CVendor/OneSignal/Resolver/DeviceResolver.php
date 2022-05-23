<?php

use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_DeviceResolver implements CVendor_OneSignal_Resolver_ResolverInterface {
    private $config;

    /**
     * @var bool
     */
    private $isNewDevice;

    /**
     * DeviceResolver constructor.
     *
     * @param Config $config
     * @param bool   $isNewDevice
     */
    public function __construct(CVendor_OneSignal_Config $config, $isNewDevice = false) {
        $this->config = $config;
        $this->isNewDevice = $isNewDevice;
    }

    /**
     * @inheritdoc
     */
    public function resolve(array $data) {
        $resolver = (new OptionsResolver())
            ->setDefined('identifier')
            ->setAllowedTypes('identifier', 'string')
            ->setDefined('identifier_auth_hash')
            ->setAllowedTypes('identifier_auth_hash', 'string')
            ->setDefined('language')
            ->setAllowedTypes('language', 'string')
            ->setDefined('timezone')
            ->setAllowedTypes('timezone', 'int')
            ->setDefined('game_version')
            ->setAllowedTypes('game_version', 'string')
            ->setDefined('device_model')
            ->setAllowedTypes('device_model', 'string')
            ->setDefined('device_os')
            ->setAllowedTypes('device_os', 'string')
            ->setDefined('ad_id')
            ->setAllowedTypes('ad_id', 'string')
            ->setDefined('sdk')
            ->setAllowedTypes('sdk', 'string')
            ->setDefined('session_count')
            ->setAllowedTypes('session_count', 'int')
            ->setDefined('tags')
            ->setAllowedTypes('tags', 'array')
            ->setDefined('amount_spent')
            ->setAllowedTypes('amount_spent', 'float')
            ->setDefined('created_at')
            ->setAllowedTypes('created_at', 'int')
            ->setDefined('playtime')
            ->setAllowedTypes('playtime', 'int')
            ->setDefined('badge_count')
            ->setAllowedTypes('badge_count', 'int')
            ->setDefined('last_active')
            ->setAllowedTypes('last_active', 'int')
            ->setDefined('notification_types')
            ->setAllowedTypes('notification_types', 'int')
            ->setAllowedValues('notification_types', [1, -2])
            ->setDefined('test_type')
            ->setAllowedTypes('test_type', 'int')
            ->setAllowedValues('test_type', [1, 2])
            ->setDefined('long')
            ->setAllowedTypes('long', 'double')
            ->setDefined('lat')
            ->setAllowedTypes('lat', 'double')
            ->setDefined('country')
            ->setAllowedTypes('country', 'string')
            ->setDefined('external_user_id')
            ->setAllowedTypes('external_user_id', 'string')
            ->setDefined('external_user_id_auth_hash')
            ->setAllowedTypes('external_user_id_auth_hash', 'string')
            ->setDefault('app_id', $this->config->getApplicationId())
            ->setAllowedTypes('app_id', 'string');

        if ($this->isNewDevice) {
            $resolver
                ->setRequired('device_type')
                ->setAllowedTypes('device_type', 'int')
                ->setAllowedValues('device_type', [
                    CVendor_OneSignal_Devices::IOS,
                    CVendor_OneSignal_Devices::ANDROID,
                    CVendor_OneSignal_Devices::AMAZON,
                    CVendor_OneSignal_Devices::WINDOWS_PHONE,
                    CVendor_OneSignal_Devices::WINDOWS_PHONE_MPNS,
                    CVendor_OneSignal_Devices::CHROME_APP,
                    CVendor_OneSignal_Devices::CHROME_WEB,
                    CVendor_OneSignal_Devices::WINDOWS_PHONE_WNS,
                    CVendor_OneSignal_Devices::SAFARI,
                    CVendor_OneSignal_Devices::FIREFOX,
                    CVendor_OneSignal_Devices::MACOS,
                    CVendor_OneSignal_Devices::ALEXA,
                    CVendor_OneSignal_Devices::EMAIL,
                    CVendor_OneSignal_Devices::HUAWEI,
                    CVendor_OneSignal_Devices::SMS,
                ]);
        } else {
            $resolver
                ->setDefined('ip')
                ->setAllowedTypes('ip', 'string')
                ->setAllowedValues('ip', static function ($ip) {
                    return (bool) filter_var($ip, FILTER_VALIDATE_IP);
                });
        }

        return $resolver->resolve($data);
    }

    /**
     * @param bool $isNewDevice
     */
    public function setIsNewDevice($isNewDevice) {
        $this->isNewDevice = $isNewDevice;
    }

    /**
     * @return bool
     */
    public function getIsNewDevice() {
        return $this->isNewDevice;
    }
}
