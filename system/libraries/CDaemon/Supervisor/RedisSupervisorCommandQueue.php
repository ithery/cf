<?php

class CDaemon_Supervisor_RedisSupervisorCommandQueue implements CDaemon_Supervisor_Contract_SupervisorCommandQueueInterface {
    /**
     * The Redis connection instance.
     *
     * @var \CRedis_FactoryInterface
     */
    public $redis;

    /**
     * Create a new command queue instance.
     *
     * @return void
     */
    public function __construct() {
        $this->redis = CRedis::instance();
    }

    /**
     * Push a command onto a given queue.
     *
     * @param string $name
     * @param string $command
     * @param array  $options
     *
     * @return void
     */
    public function push($name, $command, array $options = []) {
        $this->connection()->rpush('commands:' . $name, json_encode([
            'command' => $command,
            'options' => $options,
        ]));
    }

    /**
     * Get the pending commands for a given queue name.
     *
     * @param string $name
     *
     * @return array
     */
    public function pending($name) {
        $length = $this->connection()->llen('commands:' . $name);

        if ($length < 1) {
            return [];
        }

        $results = $this->connection()->pipeline(function ($pipe) use ($name, $length) {
            $pipe->lrange('commands:' . $name, 0, $length - 1);

            $pipe->ltrim('commands:' . $name, $length, -1);
        });

        return c::collect($results[0])->map(function ($result) {
            return (object) json_decode($result, true);
        })->all();
    }

    /**
     * Flush the command queue for a given queue name.
     *
     * @param string $name
     *
     * @return void
     */
    public function flush($name) {
        $this->connection()->del('commands:' . $name);
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
