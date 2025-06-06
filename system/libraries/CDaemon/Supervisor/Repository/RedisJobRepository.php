<?php

use Carbon\CarbonImmutable;

class CDaemon_Supervisor_Repository_RedisJobRepository implements CDaemon_Supervisor_Contract_JobRepositoryInterface {
    /**
     * The Redis connection instance.
     *
     * @var \CRedis_FactoryInterface
     */
    public $redis;

    /**
     * The keys stored on the job hashes.
     *
     * @var array
     */
    public $keys = [
        'id', 'connection', 'queue', 'name', 'status', 'payload',
        'exception', 'context', 'failed_at', 'completed_at', 'retried_by',
        'reserved_at',
    ];

    /**
     * The number of minutes until recently failed jobs should be purged.
     *
     * @var int
     */
    public $recentFailedJobExpires;

    /**
     * The number of minutes until recent jobs should be purged.
     *
     * @var int
     */
    public $recentJobExpires;

    /**
     * The number of minutes until pending jobs should be purged.
     *
     * @var int
     */
    public $pendingJobExpires;

    /**
     * The number of minutes until completed jobs should be purged.
     *
     * @var int
     */
    public $completedJobExpires;

    /**
     * The number of minutes until failed jobs should be purged.
     *
     * @var int
     */
    public $failedJobExpires;

    /**
     * The number of minutes until monitored jobs should be purged.
     *
     * @var int
     */
    public $monitoredJobExpires;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct() {
        $this->redis = CRedis::instance();
        $this->recentJobExpires = CF::config('daemon.supervisor.trim.recent', 60);
        $this->pendingJobExpires = CF::config('daemon.supervisor.trim.pending', 60);
        $this->completedJobExpires = CF::config('daemon.supervisor.trim.completed', 60);
        $this->failedJobExpires = CF::config('daemon.supervisor.trim.failed', 10080);
        $this->recentFailedJobExpires = CF::config('daemon.supervisor.trim.recent_failed', $this->failedJobExpires);
        $this->monitoredJobExpires = CF::config('daemon.supervisor.trim.monitored', 10080);
    }

    /**
     * Get the next job ID that should be assigned.
     *
     * @return string
     */
    public function nextJobId() {
        return (string) $this->connection()->incr('job_id');
    }

    /**
     * Get the total count of recent jobs.
     *
     * @return int
     */
    public function totalRecent() {
        return $this->connection()->zcard('recent_jobs');
    }

    /**
     * Get the total count of failed jobs.
     *
     * @return int
     */
    public function totalFailed() {
        return $this->connection()->zcard('failed_jobs');
    }

    /**
     * Get a chunk of recent jobs.
     *
     * @param null|string $afterIndex
     *
     * @return \CCollection
     */
    public function getRecent($afterIndex = null) {
        return $this->getJobsByType('recent_jobs', $afterIndex);
    }

    /**
     * Get a chunk of failed jobs.
     *
     * @param null|string $afterIndex
     *
     * @return \CCollection
     */
    public function getFailed($afterIndex = null) {
        return $this->getJobsByType('failed_jobs', $afterIndex);
    }

    /**
     * Get a chunk of pending jobs.
     *
     * @param null|string $afterIndex
     *
     * @return \CCollection
     */
    public function getPending($afterIndex = null) {
        return $this->getJobsByType('pending_jobs', $afterIndex);
    }

    /**
     * Get a chunk of completed jobs.
     *
     * @param null|string $afterIndex
     *
     * @return \CCollection
     */
    public function getCompleted($afterIndex = null) {
        return $this->getJobsByType('completed_jobs', $afterIndex);
    }

    /**
     * Get a chunk of silenced jobs.
     *
     * @param null|string $afterIndex
     *
     * @return \CCollection
     */
    public function getSilenced($afterIndex = null) {
        return $this->getJobsByType('silenced_jobs', $afterIndex);
    }

    /**
     * Get the count of recent jobs.
     *
     * @return int
     */
    public function countRecent() {
        return $this->countJobsByType('recent_jobs');
    }

    /**
     * Get the count of failed jobs.
     *
     * @return int
     */
    public function countFailed() {
        return $this->countJobsByType('failed_jobs');
    }

    /**
     * Get the count of pending jobs.
     *
     * @return int
     */
    public function countPending() {
        return $this->countJobsByType('pending_jobs');
    }

    /**
     * Get the count of completed jobs.
     *
     * @return int
     */
    public function countCompleted() {
        return $this->countJobsByType('completed_jobs');
    }

    /**
     * Get the count of silenced jobs.
     *
     * @return int
     */
    public function countSilenced() {
        return $this->countJobsByType('silenced_jobs');
    }

    /**
     * Get the count of the recently failed jobs.
     *
     * @return int
     */
    public function countRecentlyFailed() {
        return $this->countJobsByType('recent_failed_jobs');
    }

    /**
     * Get a chunk of jobs from the given type set.
     *
     * @param string $type
     * @param string $afterIndex
     *
     * @return \CCollection
     */
    protected function getJobsByType($type, $afterIndex) {
        $afterIndex = $afterIndex === null ? -1 : $afterIndex;

        return $this->getJobs($this->connection()->zrange(
            $type,
            $afterIndex + 1,
            $afterIndex + 50
        ), $afterIndex + 1);
    }

    /**
     * Get the number of jobs in a given type set.
     *
     * @param string $type
     *
     * @return int
     */
    protected function countJobsByType($type) {
        $minutes = $this->minutesForType($type);

        return $this->connection()->zcount(
            $type,
            '-inf',
            CarbonImmutable::now()->subMinutes($minutes)->getTimestamp() * -1
        );
    }

    /**
     * Get the number of minutes to count for a given type set.
     *
     * @param string $type
     *
     * @return int
     */
    protected function minutesForType($type) {
        switch ($type) {
            case 'failed_jobs':
                return $this->failedJobExpires;
            case 'recent_failed_jobs':
                return $this->recentFailedJobExpires;
            case 'pending_jobs':
                return $this->pendingJobExpires;
            case 'completed_jobs':
                return $this->completedJobExpires;
            case 'silenced_jobs':
                return $this->completedJobExpires;
            default:
                return $this->recentJobExpires;
        }
    }

    /**
     * Retrieve the jobs with the given IDs.
     *
     * @param array $ids
     * @param mixed $indexFrom
     *
     * @return \CCollection
     */
    public function getJobs(array $ids, $indexFrom = 0) {
        $jobs = $this->connection()->pipeline(function ($pipe) use ($ids) {
            foreach ($ids as $id) {
                $pipe->hmget($id, $this->keys);
            }
        });

        return $this->indexJobs(c::collect($jobs)->filter(function ($job) {
            $job = is_array($job) ? array_values($job) : null;

            return is_array($job) && $job[0] !== null && $job[0] !== false;
        })->values(), $indexFrom);
    }

    /**
     * Index the given jobs from the given index.
     *
     * @param \CCollection $jobs
     * @param int          $indexFrom
     *
     * @return \CCollection
     */
    protected function indexJobs($jobs, $indexFrom) {
        return $jobs->map(function ($job) use (&$indexFrom) {
            $job = (object) array_combine($this->keys, $job);

            $job->index = $indexFrom;

            $indexFrom++;

            return $job;
        });
    }

    /**
     * Insert the job into storage.
     *
     * @param string                               $connection
     * @param string                               $queue
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     *
     * @return void
     */
    public function pushed($connection, $queue, CDaemon_Supervisor_Queue_JobPayload $payload) {
        $this->connection()->pipeline(function ($pipe) use ($connection, $queue, $payload) {
            $this->storeJobReference($pipe, 'recent_jobs', $payload);
            $this->storeJobReference($pipe, 'pending_jobs', $payload);

            $time = str_replace(',', '.', microtime(true));

            $pipe->hmset($payload->id(), [
                'id' => $payload->id(),
                'connection' => $connection,
                'queue' => $queue,
                'name' => $payload->decoded['displayName'],
                'status' => 'pending',
                'payload' => $payload->value,
                'created_at' => $time,
                'updated_at' => $time,
            ]);

            $pipe->expireat(
                $payload->id(),
                CarbonImmutable::now()->addMinutes($this->pendingJobExpires)->getTimestamp()
            );
        });
    }

    /**
     * Mark the job as reserved.
     *
     * @param string                               $connection
     * @param string                               $queue
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     *
     * @return void
     */
    public function reserved($connection, $queue, CDaemon_Supervisor_Queue_JobPayload $payload) {
        $time = str_replace(',', '.', microtime(true));

        $this->connection()->hmset(
            $payload->id(),
            [
                'status' => 'reserved',
                'payload' => $payload->value,
                'updated_at' => $time,
                'reserved_at' => $time,
            ]
        );
    }

    /**
     * Mark the job as released / pending.
     *
     * @param string                               $connection
     * @param string                               $queue
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     *
     * @return void
     */
    public function released($connection, $queue, CDaemon_Supervisor_Queue_JobPayload $payload) {
        $this->connection()->hmset(
            $payload->id(),
            [
                'status' => 'pending',
                'payload' => $payload->value,
                'updated_at' => str_replace(',', '.', microtime(true)),
            ]
        );
    }

    /**
     * Mark the job as completed and monitored.
     *
     * @param string                               $connection
     * @param string                               $queue
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     *
     * @return void
     */
    public function remember($connection, $queue, CDaemon_Supervisor_Queue_JobPayload $payload) {
        $this->connection()->pipeline(function ($pipe) use ($connection, $queue, $payload) {
            $this->storeJobReference($pipe, 'monitored_jobs', $payload);

            $pipe->hmset(
                $payload->id(),
                [
                    'id' => $payload->id(),
                    'connection' => $connection,
                    'queue' => $queue,
                    'name' => $payload->decoded['displayName'],
                    'status' => 'completed',
                    'payload' => $payload->value,
                    'completed_at' => str_replace(',', '.', microtime(true)),
                ]
            );

            $pipe->expireat(
                $payload->id(),
                CarbonImmutable::now()->addMinutes($this->monitoredJobExpires)->getTimestamp()
            );
        });
    }

    /**
     * Mark the given jobs as released / pending.
     *
     * @param string       $connection
     * @param string       $queue
     * @param \CCollection $payloads
     *
     * @return void
     */
    public function migrated($connection, $queue, CCollection $payloads) {
        $this->connection()->pipeline(function ($pipe) use ($payloads) {
            foreach ($payloads as $payload) {
                $pipe->hmset(
                    $payload->id(),
                    [
                        'status' => 'pending',
                        'payload' => $payload->value,
                        'updated_at' => str_replace(',', '.', microtime(true)),
                    ]
                );
            }
        });
    }

    /**
     * Handle the storage of a completed job.
     *
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     * @param bool                                 $failed
     *
     * @return void
     */
    public function completed(CDaemon_Supervisor_Queue_JobPayload $payload, $failed = false) {
        if ($payload->isRetry()) {
            $this->updateRetryInformationOnParent($payload, $failed);
        }

        $this->connection()->pipeline(function ($pipe) use ($payload) {
            $this->storeJobReference($pipe, 'completed_jobs', $payload);
            $this->removeJobReference($pipe, 'pending_jobs', $payload);

            $pipe->hmset(
                $payload->id(),
                [
                    'status' => 'completed',
                    'completed_at' => str_replace(',', '.', microtime(true)),
                ]
            );

            $pipe->expireat($payload->id(), CarbonImmutable::now()->addMinutes($this->completedJobExpires)->getTimestamp());
        });
    }

    /**
     * Update the retry status of a job's parent.
     *
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     * @param bool                                 $failed
     *
     * @return void
     */
    protected function updateRetryInformationOnParent(CDaemon_Supervisor_Queue_JobPayload $payload, $failed) {
        if ($retries = $this->connection()->hget($payload->retryOf(), 'retried_by')) {
            $retries = $this->updateRetryStatus(
                $payload,
                json_decode($retries, true),
                $failed
            );

            $this->connection()->hset(
                $payload->retryOf(),
                'retried_by',
                json_encode($retries)
            );
        }
    }

    /**
     * Update the retry status of a job in a retry array.
     *
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     * @param array                                $retries
     * @param bool                                 $failed
     *
     * @return array
     */
    protected function updateRetryStatus(CDaemon_Supervisor_Queue_JobPayload $payload, $retries, $failed) {
        return c::collect($retries)->map(function ($retry) use ($payload, $failed) {
            return $retry['id'] === $payload->id()
                    ? carr::set($retry, 'status', $failed ? 'failed' : 'completed')
                    : $retry;
        })->all();
    }

    /**
     * Delete the given monitored jobs by IDs.
     *
     * @param array $ids
     *
     * @return void
     */
    public function deleteMonitored(array $ids) {
        $this->connection()->pipeline(function ($pipe) use ($ids) {
            foreach ($ids as $id) {
                $pipe->expireat($id, CarbonImmutable::now()->addDays(7)->getTimestamp());
            }
        });
    }

    /**
     * Trim the recent job list.
     *
     * @return void
     */
    public function trimRecentJobs() {
        $this->connection()->pipeline(function ($pipe) {
            $pipe->zremrangebyscore(
                'recent_jobs',
                CarbonImmutable::now()->subMinutes($this->recentJobExpires)->getTimestamp() * -1,
                '+inf'
            );

            $pipe->zremrangebyscore(
                'recent_failed_jobs',
                CarbonImmutable::now()->subMinutes($this->recentFailedJobExpires)->getTimestamp() * -1,
                '+inf'
            );

            $pipe->zremrangebyscore(
                'pending_jobs',
                CarbonImmutable::now()->subMinutes($this->pendingJobExpires)->getTimestamp() * -1,
                '+inf'
            );

            $pipe->zremrangebyscore(
                'completed_jobs',
                CarbonImmutable::now()->subMinutes($this->completedJobExpires)->getTimestamp() * -1,
                '+inf'
            );
        });
    }

    /**
     * Trim the failed job list.
     *
     * @return void
     */
    public function trimFailedJobs() {
        $this->connection()->zremrangebyscore(
            'failed_jobs',
            CarbonImmutable::now()->subMinutes($this->failedJobExpires)->getTimestamp() * -1,
            '+inf'
        );
    }

    /**
     * Trim the monitored job list.
     *
     * @return void
     */
    public function trimMonitoredJobs() {
        $this->connection()->zremrangebyscore(
            'monitored_jobs',
            CarbonImmutable::now()->subMinutes($this->monitoredJobExpires)->getTimestamp() * -1,
            '+inf'
        );
    }

    /**
     * Find a failed job by ID.
     *
     * @param string $id
     *
     * @return null|\stdClass
     */
    public function findFailed($id) {
        $attributes = $this->connection()->hmget(
            $id,
            $this->keys
        );

        $job = is_array($attributes) && $attributes[0] !== null ? (object) array_combine($this->keys, $attributes) : null;

        if ($job && $job->status !== 'failed') {
            return;
        }

        return $job;
    }

    /**
     * Mark the job as failed.
     *
     * @param \Exception                           $exception
     * @param string                               $connection
     * @param string                               $queue
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     *
     * @return void
     */
    public function failed($exception, $connection, $queue, CDaemon_Supervisor_Queue_JobPayload $payload) {
        $this->connection()->pipeline(function ($pipe) use ($exception, $connection, $queue, $payload) {
            $this->storeJobReference($pipe, 'failed_jobs', $payload);
            $this->storeJobReference($pipe, 'recent_failed_jobs', $payload);
            $this->removeJobReference($pipe, 'pending_jobs', $payload);
            $this->removeJobReference($pipe, 'completed_jobs', $payload);

            $context = 'context';
            $pipe->hmset(
                $payload->id(),
                [
                    'id' => $payload->id(),
                    'connection' => $connection,
                    'queue' => $queue,
                    'name' => $payload->decoded['displayName'],
                    'status' => 'failed',
                    'payload' => $payload->value,
                    'exception' => (string) $exception,
                    'context' => method_exists($exception, $context)
                        ? json_encode($exception->$context())
                        : null,
                    'failed_at' => str_replace(',', '.', microtime(true)),
                ]
            );

            $pipe->expireat(
                $payload->id(),
                CarbonImmutable::now()->addMinutes($this->failedJobExpires)->getTimestamp()
            );
        });
    }

    /**
     * Store the look-up references for a job.
     *
     * @param mixed                                $pipe
     * @param string                               $key
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     *
     * @return void
     */
    protected function storeJobReference($pipe, $key, CDaemon_Supervisor_Queue_JobPayload $payload) {
        $pipe->zadd($key, str_replace(',', '.', microtime(true) * -1), $payload->id());
    }

    /**
     * Remove the look-up references for a job.
     *
     * @param mixed                                $pipe
     * @param string                               $key
     * @param \CDaemon_Supervisor_Queue_JobPayload $payload
     *
     * @return void
     */
    protected function removeJobReference($pipe, $key, CDaemon_Supervisor_Queue_JobPayload $payload) {
        $pipe->zrem($key, $payload->id());
    }

    /**
     * Store the retry job ID on the original job record.
     *
     * @param string $id
     * @param string $retryId
     *
     * @return void
     */
    public function storeRetryReference($id, $retryId) {
        $retries = json_decode($this->connection()->hget($id, 'retried_by') ?: '[]');

        $retries[] = [
            'id' => $retryId,
            'status' => 'pending',
            'retried_at' => CarbonImmutable::now()->getTimestamp(),
        ];

        $this->connection()->hmset($id, ['retried_by' => json_encode($retries)]);
    }

    /**
     * Delete a failed job by ID.
     *
     * @param string $id
     *
     * @return int
     */
    public function deleteFailed($id) {
        return $this->connection()->zrem('failed_jobs', $id) != 1
            ? 0
            : $this->connection()->del($id);
    }

    /**
     * Delete pending and reserved jobs for a queue.
     *
     * @param string $queue
     *
     * @return int
     */
    public function purge($queue) {
        return $this->connection()->eval(
            CDaemon_Supervisor_LuaScripts::purge(),
            2,
            'recent_jobs',
            'pending_jobs',
            CF::config('daemon.supervisor.prefix'),
            $queue
        );
    }

    /**
     * Get the Redis connection instance.
     *
     * @return \CRedis_AbstractConnection
     */
    protected function connection() {
        return $this->redis->connection('supervisor');
    }
}
