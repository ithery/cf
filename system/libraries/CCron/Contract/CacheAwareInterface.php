<?php

interface CCron_Contract_CacheAwareInterface {
    /**
     * Specify the cache store that should be used.
     *
     * @param string $store
     *
     * @return $this
     */
    public function useStore($store);
}
