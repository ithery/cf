<?php

use CarbonV3\CarbonImmutable;

class CQueue_BatchRepository implements CQueue_Contract_PrunableBatchRepositoryInterface {
    /**
     * The batch factory instance.
     *
     * @var \CQueue_BatchFactory
     */
    protected $factory;

    /**
     * The database connection instance.
     *
     * @var \CDatabase
     */
    protected $connection;

    /**
     * The database table to use to store batch information.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new batch repository instance.
     *
     * @param \CDatabase $connection
     * @param string     $table
     */
    public function __construct(CDatabase $connection, $table) {
        $this->factory = CQueue::batchFactory();
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Retrieve a list of batches.
     *
     * @param int   $limit
     * @param mixed $before
     *
     * @return \CQueue_Batch[]
     */
    public function get($limit = 50, $before = null) {
        return $this->connection->table($this->table)
            ->orderByDesc('id')
            ->take($limit)
            ->when($before, function ($q) use ($before) {
                return $q->where('id', '<', $before);
            })
            ->get()
            ->map(function ($batch) {
                return $this->toBatch($batch);
            })
            ->all();
    }

    /**
     * Retrieve information about an existing batch.
     *
     * @param string $batchId
     *
     * @return null|\CQueue_Batch
     */
    public function find($batchId) {
        $batch = $this->connection->table($this->table)
            ->where('id', $batchId)
            ->first();

        if ($batch) {
            return $this->toBatch($batch);
        }

        return null;
    }

    /**
     * Store a new pending batch.
     *
     * @param \CQueue_PendingBatch $batch
     *
     * @return \CQueue_Batch
     */
    public function store(CQueue_PendingBatch $batch) {
        $id = (string) cstr::orderedUuid();

        $this->connection->table($this->table)->insert([
            'id' => $id,
            'org_id' => $batch->orgId,
            'app_code' => CF::appCode(),
            'name' => $batch->name,
            'total_jobs' => 0,
            'pending_jobs' => 0,
            'failed_jobs' => 0,
            'failed_job_ids' => '[]',
            'options' => $this->serialize($batch->options),
            'created' => c::now(),
            'createdby' => c::base()->username(),
            'updated' => c::now(),
            'updatedby' => c::base()->username(),
            'cancelled_at' => null,
            'finished_at' => null,
        ]);

        return $this->find($id);
    }

    /**
     * Increment the total number of jobs within the batch.
     *
     * @param string $batchId
     * @param int    $amount
     *
     * @return void
     */
    public function incrementTotalJobs($batchId, $amount) {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'total_jobs' => new CDatabase_Query_Expression('total_jobs + ' . $amount),
            'pending_jobs' => new CDatabase_Query_Expression('pending_jobs + ' . $amount),
            'finished_at' => null,
        ]);
    }

    /**
     * Decrement the total number of pending jobs for the batch.
     *
     * @param string $batchId
     * @param string $jobId
     *
     * @return \CQueue_UpdatedBatchJobCounts
     */
    public function decrementPendingJobs($batchId, $jobId) {
        $values = $this->updateAtomicValues($batchId, function ($batch) use ($jobId) {
            return [
                'pending_jobs' => $batch->pending_jobs - 1,
                'failed_jobs' => $batch->failed_jobs,
                'failed_job_ids' => json_encode(array_values(array_diff(json_decode($batch->failed_job_ids, true), [$jobId]))),
            ];
        });

        return new CQueue_UpdatedBatchJobCounts(
            $values['pending_jobs'],
            $values['failed_jobs']
        );
    }

    /**
     * Increment the total number of failed jobs for the batch.
     *
     * @param string $batchId
     * @param string $jobId
     *
     * @return \CQueue_UpdatedBatchJobCounts
     */
    public function incrementFailedJobs($batchId, $jobId) {
        $values = $this->updateAtomicValues($batchId, function ($batch) use ($jobId) {
            return [
                'pending_jobs' => $batch->pending_jobs,
                'failed_jobs' => $batch->failed_jobs + 1,
                'failed_job_ids' => json_encode(array_values(array_unique(array_merge(json_decode($batch->failed_job_ids, true), [$jobId])))),
            ];
        });

        return new CQueue_UpdatedBatchJobCounts(
            $values['pending_jobs'],
            $values['failed_jobs']
        );
    }

    /**
     * Update an atomic value within the batch.
     *
     * @param string   $batchId
     * @param \Closure $callback
     *
     * @return null|int
     */
    protected function updateAtomicValues(string $batchId, Closure $callback) {
        return $this->connection->transaction(function () use ($batchId, $callback) {
            $batch = $this->connection->table($this->table)->where('id', $batchId)
                ->lockForUpdate()
                ->first();

            return is_null($batch) ? [] : c::tap($callback($batch), function ($values) use ($batchId) {
                $this->connection->table($this->table)->where('id', $batchId)->update($values);
            });
        });
    }

    /**
     * Mark the batch that has the given ID as finished.
     *
     * @param string $batchId
     *
     * @return void
     */
    public function markAsFinished($batchId) {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'finished_at' => c::now(),
        ]);
    }

    /**
     * Cancel the batch that has the given ID.
     *
     * @param string $batchId
     *
     * @return void
     */
    public function cancel($batchId) {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'cancelled_at' => c::now(),
            'finished_at' => c::now(),
        ]);
    }

    /**
     * Delete the batch that has the given ID.
     *
     * @param string $batchId
     *
     * @return void
     */
    public function delete($batchId) {
        $this->connection->table($this->table)->where('id', $batchId)->delete();
    }

    /**
     * Prune all of the entries older than the given date.
     *
     * @param \DateTimeInterface $before
     *
     * @return int
     */
    public function prune(DateTimeInterface $before) {
        $query = $this->connection->table($this->table)
            ->whereNotNull('finished_at')
            ->where('finished_at', '<', $before);

        $totalDeleted = 0;

        do {
            $deleted = $query->take(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }

    /**
     * Prune all of the unfinished entries older than the given date.
     *
     * @param \DateTimeInterface $before
     *
     * @return int
     */
    public function pruneUnfinished(DateTimeInterface $before) {
        $query = $this->connection->table($this->table)
            ->whereNull('finished_at')
            ->where('created', '<', $before);

        $totalDeleted = 0;

        do {
            $deleted = $query->take(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }

    /**
     * Prune all of the cancelled entries older than the given date.
     *
     * @param \DateTimeInterface $before
     *
     * @return int
     */
    public function pruneCancelled(DateTimeInterface $before) {
        $query = $this->connection->table($this->table)
            ->whereNotNull('cancelled_at')
            ->where('created', '<', $before->getTimestamp());

        $totalDeleted = 0;

        do {
            $deleted = $query->take(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }

    /**
     * Execute the given Closure within a storage specific transaction.
     *
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function transaction($callback) {
        return $this->connection->transaction(function () use ($callback) {
            return $callback();
        });
    }

    /**
     * Serialize the given value.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function serialize($value) {
        $serialized = serialize($value);
        $isPostgres = false;

        return $isPostgres
            ? base64_encode($serialized)
            : $serialized;
    }

    /**
     * Unserialize the given value.
     *
     * @param string $serialized
     *
     * @return mixed
     */
    protected function unserialize($serialized) {
        $isPostgres = false;
        if ($isPostgres
            && !cstr::contains($serialized, [':', ';'])
        ) {
            $serialized = base64_decode($serialized);
        }

        return unserialize($serialized);
    }

    /**
     * Convert the given raw batch to a Batch object.
     *
     * @param object $batch
     *
     * @return \CQueue_Batch
     */
    protected function toBatch($batch) {
        return $this->factory->make(
            $this,
            $batch->id,
            $batch->name,
            (int) $batch->total_jobs,
            (int) $batch->pending_jobs,
            (int) $batch->failed_jobs,
            json_decode($batch->failed_job_ids, true),
            $this->unserialize($batch->options),
            CarbonImmutable::parse($batch->created),
            $batch->cancelled_at ? CarbonImmutable::parse($batch->cancelled_at) : $batch->cancelled_at,
            $batch->finished_at ? CarbonImmutable::parse($batch->finished_at) : $batch->finished_at
        );
    }

    /**
     * Get the underlying database connection.
     *
     * @return \CDatabase
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Set the underlying database connection.
     *
     * @param \CDatabase $connection
     *
     * @return void
     */
    public function setConnection(CDatabase $connection) {
        $this->connection = $connection;
    }
}
