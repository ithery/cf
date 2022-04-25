<?php

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_AppOutcomesResolver implements CVendor_OneSignal_Resolver_ResolverInterface {
    /**
     * @inheritdoc
     */
    public function resolve(array $data) {
        return (new OptionsResolver())
            ->setDefined('outcome_names')
            ->setAllowedTypes('outcome_names', 'string[]')
            ->setDefined('outcome_time_range')
            ->setAllowedTypes('outcome_time_range', 'string')
            ->setAllowedValues('outcome_time_range', [CVendor_OneSignal_Apps::OUTCOME_TIME_RANGE_HOUR, CVendor_OneSignal_Apps::OUTCOME_TIME_RANGE_DAY, CVendor_OneSignal_Apps::OUTCOME_TIME_RANGE_MONTH])
            ->setDefault('outcome_time_range', CVendor_OneSignal_Apps::OUTCOME_TIME_RANGE_HOUR)
            ->setDefined('outcome_platforms')
            ->setAllowedTypes('outcome_platforms', 'int[]')
            ->setAllowedValues('outcome_platforms', static function (array $platforms): bool {
                $intersect = array_intersect($platforms, [
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

                return count($intersect) === count($platforms);
            })
            ->setNormalizer('outcome_platforms', static function (Options $options, array $value): string {
                return implode(',', $value);
            })
            ->setDefined('outcome_attribution')
            ->setAllowedTypes('outcome_attribution', 'string')
            ->setAllowedValues('outcome_attribution', [
                CVendor_OneSignal_Apps::OUTCOME_ATTRIBUTION_TOTAL,
                CVendor_OneSignal_Apps::OUTCOME_ATTRIBUTION_DIRECT,
                CVendor_OneSignal_Apps::OUTCOME_ATTRIBUTION_INFLUENCED,
                CVendor_OneSignal_Apps::OUTCOME_ATTRIBUTION_UNATTRIBUTED,
            ])
            ->setDefault('outcome_attribution', CVendor_OneSignal_Apps::OUTCOME_ATTRIBUTION_TOTAL)
            ->setRequired(['outcome_names'])
            ->resolve($data);
    }
}
