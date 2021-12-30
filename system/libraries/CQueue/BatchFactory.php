<?php

use CarbonV3\CarbonImmutable;

class CQueue_BatchFactory {
    /**
     * The queue factory implementation.
     *
     * @var \CQueue_FactoryInterface
     */
    protected $queue;

    /**
     * Create a new batch factory instance.
     *
     * @param \CQueue_FactoryInterface $queue
     *
     * @return void
     */
    public function __construct(CQueue_FactoryInterface $queue) {
        $this->queue = $queue;
    }

    /**
     * Create a new batch instance.
     *
     * @param \CQueue_Contract_BatchRepositoryInterface $repository
     * @param string                                    $id
     * @param string                                    $name
     * @param int                                       $totalJobs
     * @param int                                       $pendingJobs
     * @param int                                       $failedJobs
     * @param array                                     $failedJobIds
     * @param array                                     $options
     * @param \CarbonV3\CarbonImmutable                 $createdAt
     * @param null|\CarbonV3\CarbonImmutable            $cancelledAt
     * @param null|\CarbonV3\CarbonImmutable            $finishedAt
     *
     * @return \CQueue_Batch
     */
    public function make(
        CQueue_Contract_BatchRepositoryInterface $repository,
        string $id,
        string $name,
        int $totalJobs,
        int $pendingJobs,
        int $failedJobs,
        array $failedJobIds,
        array $options,
        CarbonImmutable $createdAt,
        CarbonImmutable $cancelledAt = null,
        CarbonImmutable $finishedAt = null
    ) {
        return new CQueue_Batch($this->queue, $repository, $id, $name, $totalJobs, $pendingJobs, $failedJobs, $failedJobIds, $options, $createdAt, $cancelledAt, $finishedAt);
    }
}
