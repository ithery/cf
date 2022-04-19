<?php

use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_DeviceTagsResolver implements CVendor_OneSignal_Resolver_ResolverInterface {
    /**
     * @inheritdoc
     */
    public function resolve(array $data) {
        return (new OptionsResolver())
            ->setDefined('tags')
            ->setAllowedTypes('tags', 'array')
            ->setRequired(['tags'])
            ->resolve($data);
    }
}
