<?php

use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_NotificationHistoryResolver implements CVendor_OneSignal_Resolver_ResolverInterface {
    private $config;

    public function __construct(CVendor_OneSignal_Config $config) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function resolve(array $data) {
        return (new OptionsResolver())
            ->setRequired('events')
            ->setAllowedTypes('events', 'string')
            ->setAllowedValues('events', ['sent', 'clicked'])
            ->setRequired('email')
            ->setAllowedTypes('email', 'string')
            ->setDefault('app_id', $this->config->getApplicationId())
            ->setAllowedTypes('app_id', 'string')
            ->resolve($data);
    }
}
