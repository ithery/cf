<?php

use Carbon\CarbonImmutable;

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
     * @param \Carbon\CarbonImmutable                   $createdAt
     * @param null|\Carbon\CarbonImmutable              $cancelledAt
     * @param null|\Carbon\CarbonImmutable              $finishedAt
     *
     * @return \CQueue_Batch
     */
    public function make(
        CQueue_Contract_BatchRepositoryInterface $repository,
        $id,
        $name,
        $totalJobs,
        $pendingJobs,
        $failedJobs,
        $failedJobIds,
        $options,
        $createdAt,
        $cancelledAt = null,
        $finishedAt = null
    ) {
        return new CQueue_Batch($this->queue, $repository, $id, $name, $totalJobs, $pendingJobs, $failedJobs, $failedJobIds, $options, $createdAt, $cancelledAt, $finishedAt);
    }
}
