<?php

interface CVendor_OneSignal_Resolver_ResolverInterface {

    /**
     * Resolve option array.
     *
     * @param array $data
     *
     * @return array
     */
    public function resolve(array $data);
}
