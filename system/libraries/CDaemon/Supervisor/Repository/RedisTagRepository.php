<?php

class CDaemon_Supervisor_Repository_RedisTagRepository implements CDaemon_Supervisor_Contract_TagRepositoryInterface {
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
     * Get the currently monitored tags.
     *
     * @return array
     */
    public function monitoring() {
        return (array) $this->connection()->smembers('monitoring');
    }

    /**
     * Return the tags which are being monitored.
     *
     * @param array $tags
     *
     * @return array
     */
    public function monitored(array $tags) {
        return array_intersect($tags, $this->monitoring());
    }

    /**
     * Start monitoring the given tag.
     *
     * @param string $tag
     *
     * @return void
     */
    public function monitor($tag) {
        $this->connection()->sadd('monitoring', $tag);
    }

    /**
     * Stop monitoring the given tag.
     *
     * @param string $tag
     *
     * @return void
     */
    public function stopMonitoring($tag) {
        $this->connection()->srem('monitoring', $tag);
    }

    /**
     * Store the tags for the given job.
     *
     * @param string $id
     * @param array  $tags
     *
     * @return void
     */
    public function add($id, array $tags) {
        $this->connection()->pipeline(function ($pipe) use ($id, $tags) {
            foreach ($tags as $tag) {
                $pipe->zadd($tag, str_replace(',', '.', microtime(true)), $id);
            }
        });
    }

    /**
     * Store the tags for the given job temporarily.
     *
     * @param int    $minutes
     * @param string $id
     * @param array  $tags
     *
     * @return void
     */
    public function addTemporary($minutes, $id, array $tags) {
        $this->connection()->pipeline(function ($pipe) use ($minutes, $id, $tags) {
            foreach ($tags as $tag) {
                $pipe->zadd($tag, str_replace(',', '.', microtime(true)), $id);

                $pipe->expire($tag, $minutes * 60);
            }
        });
    }

    /**
     * Get the number of jobs matching a given tag.
     *
     * @param string $tag
     *
     * @return int
     */
    public function count($tag) {
        return $this->connection()->zcard($tag);
    }

    /**
     * Get all of the job IDs for a given tag.
     *
     * @param string $tag
     *
     * @return array
     */
    public function jobs($tag) {
        return (array) $this->connection()->zrange($tag, 0, -1);
    }

    /**
     * Paginate the job IDs for a given tag.
     *
     * @param string $tag
     * @param int    $startingAt
     * @param int    $limit
     *
     * @return array
     */
    public function paginate($tag, $startingAt = 0, $limit = 25) {
        $tags = (array) $this->connection()->zrevrange(
            $tag,
            $startingAt,
            $startingAt + $limit - 1
        );

        return c::collect($tags)->values()->mapWithKeys(function ($tag, $index) use ($startingAt) {
            return [$index + $startingAt => $tag];
        })->all();
    }

    /**
     * Remove the given job IDs from the given tag.
     *
     * @param array|string $tags
     * @param array|string $ids
     *
     * @return void
     */
    public function forgetJobs($tags, $ids) {
        $this->connection()->pipeline(function ($pipe) use ($tags, $ids) {
            foreach ((array) $tags as $tag) {
                foreach ((array) $ids as $id) {
                    $pipe->zrem($tag, $id);
                }
            }
        });
    }

    /**
     * Delete the given tag from storage.
     *
     * @param string $tag
     *
     * @return void
     */
    public function forget($tag) {
        $this->connection()->del($tag);
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
