<?php

use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_DevicePurchaseResolver implements CVendor_OneSignal_Resolver_ResolverInterface {

    /**
     * {@inheritdoc}
     */
    public function resolve(array $data) {
        $data = (new OptionsResolver())
                ->setDefined('existing')
                ->setAllowedTypes('existing', 'bool')
                ->setRequired('purchases')
                ->setAllowedTypes('purchases', 'array')
                ->resolve($data);

        foreach ($data['purchases'] as $key => $purchase) {
            $data['purchases'][$key] = (new OptionsResolver())
                    ->setRequired('sku')
                    ->setAllowedTypes('sku', 'string')
                    ->setRequired('amount')
                    ->setAllowedTypes('amount', 'float')
                    ->setRequired('iso')
                    ->setAllowedTypes('iso', 'string')
                    ->resolve($purchase);
        }

        return $data;
    }

}
