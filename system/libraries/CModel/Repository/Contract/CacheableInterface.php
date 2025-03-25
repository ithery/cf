<?php

/**
 * Interface CacheableInterface.
 */
interface CModel_Repository_Contract_CacheableInterface {
    /**
     * Set Cache Repository.
     *
     * @param CCache_RepositoryInterface $repository
     *
     * @return $this
     */
    public function setCacheRepository(CCache_RepositoryInterface $repository);

    /**
     * Return instance of Cache Repository.
     *
     * @return CacheRepository
     */
    public function getCacheRepository();

    /**
     * Get Cache key for the method.
     *
     * @param $method
     * @param $args
     *
     * @return string
     */
    public function getCacheKey($method, $args = null);

    /**
     * Get cache time.
     *
     * @return int
     */
    public function getCacheTime();

    /**
     * Skip Cache.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCache($status = true);
}
