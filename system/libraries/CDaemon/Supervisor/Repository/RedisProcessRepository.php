<?php

use Carbon\CarbonImmutable;

class CDaemon_Supervisor_Repository_RedisProcessRepository implements CDaemon_Supervisor_Contract_ProcessRepositoryInterface {
    /**
     * The Redis connection instance.
     *
     * @var \CRedis_FactoryInterface
     */
    public $redis;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct() {
        $this->redis = CRedis::instance();
    }

    /**
     * Get all of the orphan process IDs and the times they were observed.
     *
     * @param string $master
     *
     * @return array
     */
    public function allOrphans($master) {
        return $this->connection()->hgetall(
            "{$master}:orphans"
        );
    }

    /**
     * Record the given process IDs as orphaned.
     *
     * @param string $master
     * @param array  $processIds
     *
     * @return void
     */
    public function orphaned($master, array $processIds) {
        $time = CarbonImmutable::now()->getTimestamp();

        $shouldRemove = array_diff($this->connection()->hkeys(
            $key = "{$master}:orphans"
        ), $processIds);

        if (!empty($shouldRemove)) {
            $this->connection()->hdel($key, ...$shouldRemove);
        }

        $this->connection()->pipeline(function ($pipe) use ($key, $time, $processIds) {
            foreach ($processIds as $processId) {
                $pipe->hsetnx($key, $processId, $time);
            }
        });
    }

    /**
     * Get the process IDs orphaned for at least the given number of seconds.
     *
     * @param string $master
     * @param int    $seconds
     *
     * @return array
     */
    public function orphanedFor($master, $seconds) {
        $expiresAt = CarbonImmutable::now()->getTimestamp() - $seconds;

        return c::collect($this->allOrphans($master))->filter(function ($recordedAt, $_) use ($expiresAt) {
            return $expiresAt > $recordedAt;
        })->keys()->all();
    }

    /**
     * Remove the given process IDs from the orphan list.
     *
     * @param string $master
     * @param array  $processIds
     *
     * @return void
     */
    public function forgetOrphans($master, array $processIds) {
        $this->connection()->hdel(
            "{$master}:orphans",
            ...$processIds
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
