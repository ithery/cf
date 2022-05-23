<?php

class CQueue_Batch implements CInterface_Arrayable, JsonSerializable {
    /**
     * The batch ID.
     *
     * @var string
     */
    public $id;

    /**
     * The batch name.
     *
     * @var string
     */
    public $name;

    /**
     * The total number of jobs that belong to the batch.
     *
     * @var int
     */
    public $totalJobs;

    /**
     * The total number of jobs that are still pending.
     *
     * @var int
     */
    public $pendingJobs;

    /**
     * The total number of jobs that have failed.
     *
     * @var int
     */
    public $failedJobs;

    /**
     * The IDs of the jobs that have failed.
     *
     * @var array
     */
    public $failedJobIds;

    /**
     * The batch options.
     *
     * @var array
     */
    public $options;

    /**
     * The date indicating when the batch was created.
     *
     * @var \CarbonV3\CarbonImmutable
     */
    public $createdAt;

    /**
     * The date indicating when the batch was cancelled.
     *
     * @var null|\CarbonV3\CarbonImmutable
     */
    public $cancelledAt;

    /**
     * The date indicating when the batch was finished.
     *
     * @var null|\CarbonV3\CarbonImmutable
     */
    public $finishedAt;

    /**
     * The queue factory implementation.
     *
     * @var \CQueue_FactoryInterface
     */
    protected $queue;

    /**
     * The repository implementation.
     *
     * @var \CQueue_Contract_BatchRepositoryInterface
     */
    protected $repository;

    /**
     * Create a new batch instance.
     *
     * @param \CQueue_FactoryInterface                  $queue
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
     * @return void
     */
    public function __construct(
        CQueue_FactoryInterface $queue,
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
        $this->queue = $queue;
        $this->repository = $repository;
        $this->id = $id;
        $this->name = $name;
        $this->totalJobs = $totalJobs;
        $this->pendingJobs = $pendingJobs;
        $this->failedJobs = $failedJobs;
        $this->failedJobIds = $failedJobIds;
        $this->options = $options;
        $this->createdAt = $createdAt;
        $this->cancelledAt = $cancelledAt;
        $this->finishedAt = $finishedAt;
    }

    /**
     * Get a fresh instance of the batch represented by this ID.
     *
     * @return self
     */
    public function fresh() {
        return $this->repository->find($this->id);
    }

    /**
     * Add additional jobs to the batch.
     *
     * @param \CCollection|array $jobs
     *
     * @return self
     */
    public function add($jobs) {
        $count = 0;

        $jobs = CCollection::wrap($jobs)->map(function ($job) use (&$count) {
            $job = $job instanceof Closure ? CQueue_CallQueuedClosure::create($job) : $job;

            if (is_array($job)) {
                $count += count($job);

                return c::with($this->prepareBatchedChain($job), function ($chain) {
                    return $chain->first()
                        ->allOnQueue(isset($this->options['queue']) ? $this->options['queue'] : null)
                        ->allOnConnection(isset($this->options['connection']) ? $this->options['connection'] : null)
                        ->chain($chain->slice(1)->values()->all());
                });
            } else {
                $job->withBatchId($this->id);

                $count++;
            }

            return $job;
        });

        $this->repository->transaction(function () use ($jobs, $count) {
            $this->repository->incrementTotalJobs($this->id, $count);

            $this->queue->connection(isset($this->options['connection']) ? $this->options['connection'] : null)->bulk(
                $jobs->all(),
                $data = '',
                $this->options['queue'] ?? null
            );
        });

        return $this->fresh();
    }

    /**
     * Prepare a chain that exists within the jobs being added.
     *
     * @param array $chain
     *
     * @return \CCollection
     */
    protected function prepareBatchedChain(array $chain) {
        return c::collect($chain)->map(function ($job) {
            $job = $job instanceof Closure ? CQueue_CallQueuedClosure::create($job) : $job;

            return $job->withBatchId($this->id);
        });
    }

    /**
     * Get the total number of jobs that have been processed by the batch thus far.
     *
     * @return int
     */
    public function processedJobs() {
        return $this->totalJobs - $this->pendingJobs;
    }

    /**
     * Get the percentage of jobs that have been processed (between 0-100).
     *
     * @return int
     */
    public function progress() {
        return $this->totalJobs > 0 ? round(($this->processedJobs() / $this->totalJobs) * 100) : 0;
    }

    /**
     * Record that a job within the batch finished successfully, executing any callbacks if necessary.
     *
     * @param string $jobId
     *
     * @return void
     */
    public function recordSuccessfulJob($jobId) {
        $counts = $this->decrementPendingJobs($jobId);

        if ($counts->pendingJobs === 0) {
            $this->repository->markAsFinished($this->id);
        }

        if ($counts->pendingJobs === 0 && $this->hasThenCallbacks()) {
            $batch = $this->fresh();

            c::collect($this->options['then'])->each(function ($handler) use ($batch) {
                $this->invokeHandlerCallback($handler, $batch);
            });
        }

        if ($counts->allJobsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
            $batch = $this->fresh();

            c::collect($this->options['finally'])->each(function ($handler) use ($batch) {
                $this->invokeHandlerCallback($handler, $batch);
            });
        }
    }

    /**
     * Decrement the pending jobs for the batch.
     *
     * @param string $jobId
     *
     * @return \CQueue_UpdatedBatchJobCounts
     */
    public function decrementPendingJobs($jobId) {
        return $this->repository->decrementPendingJobs($this->id, $jobId);
    }

    /**
     * Determine if the batch has finished executing.
     *
     * @return bool
     */
    public function finished() {
        return !is_null($this->finishedAt);
    }

    /**
     * Determine if the batch has "success" callbacks.
     *
     * @return bool
     */
    public function hasThenCallbacks() {
        return isset($this->options['then']) && !empty($this->options['then']);
    }

    /**
     * Determine if the batch allows jobs to fail without cancelling the batch.
     *
     * @return bool
     */
    public function allowsFailures() {
        return carr::get($this->options, 'allowFailures', false) === true;
    }

    /**
     * Determine if the batch has job failures.
     *
     * @return bool
     */
    public function hasFailures() {
        return $this->failedJobs > 0;
    }

    /**
     * Record that a job within the batch failed to finish successfully, executing any callbacks if necessary.
     *
     * @param string     $jobId
     * @param \Throwable $e
     *
     * @return void
     */
    public function recordFailedJob($jobId, $e) {
        $counts = $this->incrementFailedJobs($jobId);

        if ($counts->failedJobs === 1 && !$this->allowsFailures()) {
            $this->cancel();
        }

        if ($counts->failedJobs === 1 && $this->hasCatchCallbacks()) {
            $batch = $this->fresh();

            c::collect($this->options['catch'])->each(function ($handler) use ($batch, $e) {
                $this->invokeHandlerCallback($handler, $batch, $e);
            });
        }

        if ($counts->allJobsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
            $batch = $this->fresh();

            c::collect($this->options['finally'])->each(function ($handler) use ($batch, $e) {
                $this->invokeHandlerCallback($handler, $batch, $e);
            });
        }
    }

    /**
     * Increment the failed jobs for the batch.
     *
     * @param string $jobId
     *
     * @return \CQueue_UpdatedBatchJobCounts
     */
    public function incrementFailedJobs(string $jobId) {
        return $this->repository->incrementFailedJobs($this->id, $jobId);
    }

    /**
     * Determine if the batch has "catch" callbacks.
     *
     * @return bool
     */
    public function hasCatchCallbacks() {
        return isset($this->options['catch']) && !empty($this->options['catch']);
    }

    /**
     * Determine if the batch has "then" callbacks.
     *
     * @return bool
     */
    public function hasFinallyCallbacks() {
        return isset($this->options['finally']) && !empty($this->options['finally']);
    }

    /**
     * Cancel the batch.
     *
     * @return void
     */
    public function cancel() {
        $this->repository->cancel($this->id);
    }

    /**
     * Determine if the batch has been cancelled.
     *
     * @return bool
     */
    public function canceled() {
        return $this->cancelled();
    }

    /**
     * Determine if the batch has been cancelled.
     *
     * @return bool
     */
    public function cancelled() {
        return !is_null($this->cancelledAt);
    }

    /**
     * Delete the batch from storage.
     *
     * @return void
     */
    public function delete() {
        $this->repository->delete($this->id);
    }

    /**
     * Invoke a batch callback handler.
     *
     * @param \CQueue_SerializableClosure|callable $handler
     * @param \CQueue_Batch                        $batch
     * @param null|\Throwable                      $e
     *
     * @return void
     */
    protected function invokeHandlerCallback($handler, CQueue_Batch $batch, $e = null) {
        return $handler instanceof CQueue_SerializableClosure
                    ? $handler->__invoke($batch, $e)
                    : call_user_func($handler, $batch, $e);
    }

    /**
     * Convert the batch to an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'totalJobs' => $this->totalJobs,
            'pendingJobs' => $this->pendingJobs,
            'processedJobs' => $this->processedJobs(),
            'progress' => $this->progress(),
            'failedJobs' => $this->failedJobs,
            'options' => $this->options,
            'createdAt' => $this->createdAt,
            'cancelledAt' => $this->cancelledAt,
            'finishedAt' => $this->finishedAt,
        ];
    }

    /**
     * Get the JSON serializable representation of the object.
     *
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray();
    }
}
