<?php

use Illuminate\Support\Arr;
use CarbonV3\CarbonImmutable;
use Laravel\Horizon\MasterSupervisor;
use Laravel\Horizon\Contracts\SupervisorRepository;

class CDaemon_Supervisor_Repository_RedisMasterSupervisorRepository implements CDaemon_Supervisor_Contract_MasterSupervisorRepositoryInterface {
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
     * Get the names of all the master supervisors currently running.
     *
     * @return array
     */
    public function names() {
        return $this->connection()->zrevrangebyscore(
            $this->redisKey(),
            '+inf',
            CarbonImmutable::now()->subSeconds(14)->getTimestamp()
        );
    }

    protected function redisKey() {
        return 'masters-' . cstr::slug(CF::appCode(), '_');
    }

    /**
     * Get information on all of the supervisors.
     *
     * @return array
     */
    public function all() {
        return $this->get($this->names());
    }

    /**
     * Get information on a master supervisor by name.
     *
     * @param string $name
     *
     * @return null|\stdClass
     */
    public function find($name) {
        return carr::get($this->get([$name]), 0);
    }

    /**
     * Get information on the given master supervisors.
     *
     * @param array $names
     *
     * @return array
     */
    public function get(array $names) {
        $records = $this->connection()->pipeline(function ($pipe) use ($names) {
            foreach ($names as $name) {
                $pipe->hmget('master:' . $name, ['name', 'pid', 'status', 'supervisors']);
            }
        });

        return c::collect($records)->map(function ($record) {
            $record = array_values($record);

            return !$record[0] ? null : (object) [
                'name' => $record[0],
                'pid' => $record[1],
                'status' => $record[2],
                'supervisors' => json_decode($record[3], true),
            ];
        })->filter()->all();
    }

    /**
     * Update the information about the given master supervisor.
     *
     * @param \CDaemon_Supervisor_MasterSupervisor $master
     *
     * @return void
     */
    public function update(CDaemon_Supervisor_MasterSupervisor $master) {
        $supervisors = $master->supervisors->map->name->all();

        $this->connection()->pipeline(function ($pipe) use ($master, $supervisors) {
            $pipe->hmset(
                'master:' . $master->name,
                [
                    'name' => $master->name,
                    'pid' => $master->pid(),
                    'status' => $master->working ? 'running' : 'paused',
                    'supervisors' => json_encode($supervisors),
                ]
            );

            $pipe->zadd(
                $this->redisKey(),
                CarbonImmutable::now()->getTimestamp(),
                $master->name
            );

            $pipe->expire('master:' . $master->name, 15);
        });
    }

    /**
     * Remove the master supervisor information from storage.
     *
     * @param string $name
     *
     * @return void
     */
    public function forget($name) {
        if (!$master = $this->find($name)) {
            return;
        }

        CDaemon_Supervisor::supervisorRepository()->forget(
            $master->supervisors
        );

        $this->connection()->del($this->redisKey() . ':' . $name);

        $this->connection()->zrem($this->redisKey(), $name);
    }

    /**
     * Remove expired master supervisors from storage.
     *
     * @return void
     */
    public function flushExpired() {
        $this->connection()->zremrangebyscore(
            $this->redisKey(),
            '-inf',
            CarbonImmutable::now()->subSeconds(14)->getTimestamp()
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
