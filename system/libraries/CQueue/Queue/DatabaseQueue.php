<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 4:14:29 AM
 */
class CQueue_Queue_DatabaseQueue extends CQueue_AbstractQueue {
    /**
     * The database connection instance.
     *
     * @var CDatabase
     */
    protected $database;

    /**
     * The database table that holds the jobs.
     *
     * @var string
     */
    protected $table;

    /**
     * The name of the default queue.
     *
     * @var string
     */
    protected $default;

    /**
     * The expiration time of a job.
     *
     * @var null|int
     */
    protected $retryAfter = 60;

    /**
     * Create a new database queue instance.
     *
     * @param CDatabase $database
     * @param string    $table
     * @param string    $default
     * @param int       $retryAfter
     * @param bool      $dispatchAfterCommit
     *
     * @return void
     */
    public function __construct(CDatabase $database, $table, $default = 'default', $retryAfter = 60, $dispatchAfterCommit = false) {
        $this->table = $table;
        $this->default = $default;
        $this->database = $database;
        $this->retryAfter = $retryAfter;
        $this->dispatchAfterCommit = $dispatchAfterCommit;
    }

    /**
     * Get the size of the queue.
     *
     * @param null|string $queue
     *
     * @return int
     */
    public function size($queue = null) {
        return $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->count();
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string      $job
     * @param mixed       $data
     * @param null|string $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null) {
        return $this->enqueueUsing(
            $job,
            $this->createPayload($job, $this->getQueue($queue), $data),
            $queue,
            null,
            function ($payload, $queue) {
                return $this->pushToDatabase($queue, $payload);
            }
        );
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string      $payload
     * @param null|string $queue
     * @param array       $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = []) {
        return $this->pushToDatabase($queue, $payload);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string                               $job
     * @param mixed                                $data
     * @param null|string                          $queue
     *
     * @return void
     */
    public function later($delay, $job, $data = '', $queue = null) {
        return $this->enqueueUsing(
            $job,
            $this->createPayload($job, $this->getQueue($queue), $data),
            $queue,
            $delay,
            function ($payload, $queue, $delay) {
                return $this->pushToDatabase($queue, $payload, $delay);
            }
        );
    }

    /**
     * Push an array of jobs onto the queue.
     *
     * @param array       $jobs
     * @param mixed       $data
     * @param null|string $queue
     *
     * @return mixed
     */
    public function bulk($jobs, $data = '', $queue = null) {
        $queue = $this->getQueue($queue);
        $now = $this->availableAt();

        return $this->database->table($this->table)->insert(c::collect((array) $jobs)->map(
            function ($job) use ($queue, $data, $now) {
                return $this->buildDatabaseRecord(
                    $queue,
                    $this->createPayload($job, $this->getQueue($queue), $data),
                    isset($job->delay) ? $this->availableAt($job->delay) : $now
                );
            }
        )->all());
    }

    /**
     * Release a reserved job back onto the queue.
     *
     * @param string                       $queue
     * @param CQueue_Job_DatabaseJobRecord $job
     * @param int                          $delay
     *
     * @return mixed
     */
    public function release($queue, $job, $delay) {
        return $this->pushToDatabase($queue, $job->payload, $delay, $job->attempts);
    }

    /**
     * Push a raw payload to the database with a given delay.
     *
     * @param null|string                          $queue
     * @param string                               $payload
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param int                                  $attempts
     *
     * @return mixed
     */
    protected function pushToDatabase($queue, $payload, $delay = 0, $attempts = 0) {
        return $this->database->table($this->table)->insertGetId($this->buildDatabaseRecord(
            $this->getQueue($queue),
            $payload,
            $this->availableAt($delay),
            $attempts
        ));
    }

    /**
     * Create an array to insert for the given job.
     *
     * @param null|string $queue
     * @param string      $payload
     * @param int         $availableAt
     * @param int         $attempts
     *
     * @return array
     */
    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0) {
        $dateCurrentTime = date('Y-m-d H:i:s', $this->currentTime());

        return [
            'name' => $queue,
            'app_code' => CF::appCode(),
            'org_id' => CApp_Base::orgId(),
            'attempts' => $attempts,
            'reserved_at' => null,
            'available_at' => date('Y-m-d H:i:s', $availableAt),
            'created' => $dateCurrentTime,
            'createdby' => c::base()->username(),
            'updated' => $dateCurrentTime,
            'updatedby' => c::base()->username(),
            'payload' => $payload,
        ];
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param null|string $queue
     *
     * @throws \Exception|\Throwable
     *
     * @return null|\CQueue_JobInterface
     */
    public function pop($queue = null) {
        $queue = $this->getQueue($queue);

        return $this->database->transaction(function () use ($queue) {
            if ($job = $this->getNextAvailableJob($queue)) {
                return $this->marshalJob($queue, $job);
            }
        });
    }

    /**
     * Get the next available job for the queue.
     *
     * @param null|string $queue
     *
     * @return null|\CQueue_Job_DatabaseJobRecord
     */
    protected function getNextAvailableJob($queue) {
        $job = $this->database->table($this->table)
            ->lockForUpdate()
            ->where('name', $this->getQueue($queue))
            ->where(function ($query) {
                $this->isAvailable($query);
                $this->isReservedButExpired($query);
            })
            ->orderBy($this->primaryKey(), 'asc')
            ->first();

        return $job ? new CQueue_Job_DatabaseJobRecord((object) $job) : null;
    }

    /**
     * Modify the query to check for available jobs.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return void
     */
    protected function isAvailable($query) {
        $query->where(function ($query) {
            $dateCurrentTime = date('Y-m-d H:i:s', $this->currentTime());
            $query->whereNull('reserved_at')
                ->where('available_at', '<=', $dateCurrentTime);
        });
    }

    /**
     * Modify the query to check for jobs that are reserved but have expired.
     *
     * @param CDatabase_Query_Builder $query
     *
     * @return void
     */
    protected function isReservedButExpired($query) {
        $expiration = CCarbon::now()->subSeconds($this->retryAfter)->getTimestamp();
        $expirationDate = date('Y-m-d H:i:s', $expiration);
        $query->orWhere(function ($query) use ($expirationDate) {
            $query->where('reserved_at', '<=', $expirationDate);
        });
    }

    /**
     * Marshal the reserved job into a DatabaseJob instance.
     *
     * @param string                       $queue
     * @param CQueue_Job_DatabaseJobRecord $job
     *
     * @return CQueue_Job_DatabaseJob
     */
    protected function marshalJob($queue, $job) {
        $job = $this->markJobAsReserved($job);

        return new CQueue_Job_DatabaseJob(
            $this->container,
            $this,
            $job,
            $this->connectionName,
            $queue
        );
    }

    /**
     * Mark the given job ID as reserved.
     *
     * @param CQueue_Job_DatabaseJobRecord $job
     *
     * @return CQueue_Job_DatabaseJob
     */
    protected function markJobAsReserved($job) {
        $this->database->table($this->table)->where($this->primaryKey(), $job->{$this->primaryKey()})->update([
            'reserved_at' => date('Y-m-d H:i:s', $job->touch()),
            'attempts' => $job->increment(),
        ]);

        return $job;
    }

    /**
     * Delete a reserved job from the queue.
     *
     * @param string $queue
     * @param string $id
     *
     * @throws \Exception|\Throwable
     *
     * @return void
     */
    public function deleteReserved($queue, $id) {
        $this->database->transaction(function () use ($id) {
            if ($this->database->table($this->table)->lockForUpdate()->find($id)) {
                $this->database->table($this->table)->where($this->primaryKey(), $id)->delete();
            }
        });
    }

    /**
     * Delete a reserved job from the reserved queue and release it.
     *
     * @param string                  $queue
     * @param \CQueue_Job_DatabaseJob $job
     * @param int                     $delay
     *
     * @return void
     */
    public function deleteAndRelease($queue, $job, $delay) {
        $this->database->transaction(function () use ($queue, $job, $delay) {
            if ($this->database->table($this->table)->lockForUpdate()->find($job->getJobId())) {
                $this->database->table($this->table)->where($this->primaryKey(), $job->getJobId())->delete();
            }

            $this->release($queue, $job->getJobRecord(), $delay);
        });
    }

    /**
     * Delete all of the jobs from the queue.
     *
     * @param string $queue
     *
     * @return int
     */
    public function clear($queue) {
        return $this->database->table($this->table)
            ->where('name', $this->getQueue($queue))
            ->delete();
    }

    /**
     * Get the queue or return the default.
     *
     * @param null|string $queue
     *
     * @return string
     */
    public function getQueue($queue) {
        return $queue ?: $this->default;
    }

    /**
     * Get the underlying database instance.
     *
     * @return CDatabase
     */
    public function getDatabase() {
        return $this->database;
    }

    public function primaryKey() {
        return CQueue::primaryKey($this->database, $this->table);
    }
}
