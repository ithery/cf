<?php

use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_DeviceFocusResolver implements CVendor_OneSignal_Resolver_ResolverInterface {

    /**
     * {@inheritdoc}
     */
    public function resolve(array $data) {
        return (new OptionsResolver())
                        ->setDefault('state', 'ping')
                        ->setAllowedTypes('state', 'string')
                        ->setRequired('active_time')
                        ->setAllowedTypes('active_time', 'int')
                        ->resolve($data);
    }

}
