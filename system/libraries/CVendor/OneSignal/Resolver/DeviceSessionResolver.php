<?php

use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_DeviceSessionResolver implements CVendor_OneSignal_Resolver_ResolverInterface {
    /**
     * @inheritdoc
     */
    public function resolve(array $data) {
        return (new OptionsResolver())
            ->setDefined('identifier')
            ->setAllowedTypes('identifier', 'string')
            ->setDefined('language')
            ->setAllowedTypes('language', 'string')
            ->setDefined('timezone')
            ->setAllowedTypes('timezone', 'int')
            ->setDefined('game_version')
            ->setAllowedTypes('game_version', 'string')
            ->setDefined('device_os')
            ->setAllowedTypes('device_os', 'string')
        // @todo: remove "device_model" later (this option is probably deprecated as it is removed from documentation)
            ->setDefined('device_model')
            ->setAllowedTypes('device_model', 'string')
            ->setDefined('ad_id')
            ->setAllowedTypes('ad_id', 'string')
            ->setDefined('sdk')
            ->setAllowedTypes('sdk', 'string')
            ->setDefined('tags')
            ->setAllowedTypes('tags', 'array')
            ->setDefined('device_type')
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
            ])
            ->resolve($data);
    }
}
