<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CRedis_Connector_PhpRedisConnector extends CRedis_AbstractConnector {

    /**
     * Create a new clustered PhpRedis connection.
     *
     * @param  array  $config
     * @param  array  $options
     * @return \Illuminate\Redis\Connections\PhpRedisConnection
     */
    public function connect(array $config, array $options) {
        return new CRedis_Connection_PhpRedisConnection($this->createClient(array_merge(
                                $config, $options, carr::pull($config, 'options', [])
        )));
    }

    /**
     * Create a new clustered PhpRedis connection.
     *
     * @param  array  $config
     * @param  array  $clusterOptions
     * @param  array  $options
     * @return \Illuminate\Redis\Connections\PhpRedisClusterConnection
     */
    public function connectToCluster(array $config, array $clusterOptions, array $options) {
        $options = array_merge($options, $clusterOptions, carr::pull($config, 'options', []));
        return new CRedis_Connection_PhpRedisClusterConnection($this->createRedisClusterInstance(
                        array_map([$this, 'buildClusterConnectionString'], $config), $options
        ));
    }

    /**
     * Build a single cluster seed string from array.
     *
     * @param  array  $server
     * @return string
     */
    protected function buildClusterConnectionString(array $server) {
        return $server['host'] . ':' . $server['port'] . '?' . carr::query(carr::only($server, [
                            'database', 'password', 'prefix', 'read_timeout',
        ]));
    }

    /**
     * Create the Redis client instance.
     *
     * @param  array  $config
     * @return \Redis
     *
     * @throws \LogicException
     */
    protected function createClient(array $config) {
        return CF::tap(new Redis, function ($client) use ($config) {

            $this->establishConnection($client, $config);
            if (!empty($config['password'])) {
                $client->auth($config['password']);
            }
            if (isset($config['database'])) {
                $client->select((int) $config['database']);
            }
            if (!empty($config['prefix'])) {
                $client->setOption(Redis::OPT_PREFIX, $config['prefix']);
            }
            if (!empty($config['read_timeout'])) {
                $client->setOption(Redis::OPT_READ_TIMEOUT, $config['read_timeout']);
            }
        });
    }

    /**
     * Establish a connection with the Redis host.
     *
     * @param  \Redis  $client
     * @param  array  $config
     * @return void
     */
    protected function establishConnection($client, array $config) {
        $persistent = isset($config['persistent']) ? $config['persistent'] : false;
        $parameters = [
            $config['host'],
            $config['port'],
            carr::get($config, 'timeout', 0.0),
            $persistent ? carr::get($config, 'persistent_id', null) : null,
            carr::get($config, 'retry_interval', 0),
        ];
        if (version_compare(phpversion('redis'), '3.1.3', '>=')) {
            $parameters[] = carr::get($config, 'read_timeout', 0.0);
        }
        $client->{($persistent ? 'pconnect' : 'connect')}(...$parameters);
    }

    /**
     * Create a new redis cluster instance.
     *
     * @param  array  $servers
     * @param  array  $options
     * @return \RedisCluster
     */
    protected function createRedisClusterInstance(array $servers, array $options) {
        $parameters = [
            null,
            array_values($servers),
            isset($options['timeout']) ? $options['timeout'] : 0,
            isset($options['read_timeout']) ? $options['read_timeout'] : 0,
            isset($options['persistent']) && $options['persistent'],
        ];
        if (version_compare(phpversion('redis'), '4.3.0', '>=')) {
            $parameters[] = isset($options['password']) ? $options['password'] : null;
        }
        return CF::tap(new RedisCluster(...$parameters), function ($client) use ($options) {
                    if (!empty($options['prefix'])) {
                        $client->setOption(RedisCluster::OPT_PREFIX, $options['prefix']);
                    }
                });
    }

}
