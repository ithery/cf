<?php
class CQueue_UniqueLock {
    /**
     * The cache repository implementation.
     *
     * @var \CCache_RepositoryInterface
     */
    protected $cache;

    /**
     * Create a new unique lock manager instance.
     *
     * @param \CCache_RepositoryInterface $cache
     *
     * @return void
     */
    public function __construct(CCache_RepositoryInterface $cache) {
        $this->cache = $cache;
    }

    /**
     * Attempt to acquire a lock for the given job.
     *
     * @param mixed $job
     *
     * @return bool
     */
    public function acquire($job) {
        $uniqueId = method_exists($job, 'uniqueId')
                    ? $job->uniqueId()
                    : ($job->uniqueId ?? '');

        $cache = method_exists($job, 'uniqueVia')
                    ? $job->uniqueVia()
                    : $this->cache;

        return (bool) $cache->lock(
            $key = 'laravel_unique_job:' . get_class($job) . $uniqueId,
            $job->uniqueFor ?? 0
        )->get();
    }
}
