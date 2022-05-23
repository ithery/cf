<?php

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CVendor_OneSignal_Resolver_SegmentResolver implements CVendor_OneSignal_Resolver_ResolverInterface {
    /**
     * @inheritdoc
     */
    public function resolve(array $data) {
        return (new OptionsResolver())
            ->setDefined('id')
            ->setAllowedTypes('id', 'string')
            ->setRequired('name')
            ->setAllowedTypes('name', 'string')
            ->setDefined('filters')
            ->setAllowedTypes('filters', 'array')
            ->setNormalizer('filters', function (Options $options, array $values) {
                return $this->normalizeFilters($options, $values);
            })
            ->resolve($data);
    }

    private function normalizeFilters(Options $options, array $values) {
        $filters = [];

        foreach ($values as $filter) {
            if (isset($filter['field']) || isset($filter['operator'])) {
                $filters[] = $filter;
            }
        }

        return $filters;
    }
}
