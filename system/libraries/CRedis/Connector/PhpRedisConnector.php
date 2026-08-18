<?php

class CRedis_Connector_PhpRedisConnector extends CRedis_AbstractConnector {
    /**
     * Create a new clustered PhpRedis connection.
     *
     * @param array $config
     * @param array $options
     *
     * @return \CRedis_Connection_PhpRedisConnection
     */
    public function connect(array $config, array $options) {
        $formattedOptions = carr::pull($config, 'options', []);
        if (isset($config['prefix'])) {
            $formattedOptions['prefix'] = $config['prefix'];
        }
        $connector = function () use ($config, $options, $formattedOptions) {
            return $this->createClient(array_merge(
                $config,
                $options,
                $formattedOptions
            ));
        };

        return new CRedis_Connection_PhpRedisConnection($connector(), $connector, $config);
    }

    /**
     * Create a new clustered PhpRedis connection.
     *
     * @param array $config
     * @param array $clusterOptions
     * @param array $options
     *
     * @return \CRedis_Connection_PhpRedisClusterConnection
     */
    public function connectToCluster(array $config, array $clusterOptions, array $options) {
        $options = array_merge($options, $clusterOptions, carr::pull($config, 'options', []));

        return new CRedis_Connection_PhpRedisClusterConnection($this->createRedisClusterInstance(
            array_map([$this, 'buildClusterConnectionString'], $config),
            $options
        ));
    }

    /**
     * Build a single cluster seed string from array.
     *
     * @param array $server
     *
     * @return string
     */
    protected function buildClusterConnectionString(array $server) {
        return $this->formatHost($server) . ':' . $server['port'] . '?' . carr::query(carr::only($server, [
            'database', 'password', 'prefix', 'read_timeout',
        ]));
    }

    /**
     * Create the Redis client instance.
     *
     * @param array $config
     *
     * @throws \LogicException
     *
     * @return \Redis
     */
    protected function createClient(array $config) {
        return c::tap(new Redis(), function ($client) use ($config) {
            $this->establishConnection($client, $config);
            if (!empty($config['password'])) {
                if (isset($config['username']) && $config['username'] !== '' && is_string($config['password'])) {
                    $client->auth([$config['username'], $config['password']]);
                } else {
                    $client->auth($config['password']);
                }
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

            if (!empty($config['scan'])) {
                $client->setOption(Redis::OPT_SCAN, $config['scan']);
            }

            if (!empty($config['name'])) {
                $client->client('SETNAME', $config['name']);
            }

            if (array_key_exists('serializer', $config)) {
                $client->setOption(Redis::OPT_SERIALIZER, $config['serializer']);
            }

            if (array_key_exists('compression', $config)) {
                $client->setOption(Redis::OPT_COMPRESSION, $config['compression']);
            }

            if (array_key_exists('compression_level', $config)) {
                $client->setOption(Redis::OPT_COMPRESSION_LEVEL, $config['compression_level']);
            }
        });
    }

    /**
     * Establish a connection with the Redis host.
     *
     * @param \Redis $client
     * @param array  $config
     *
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

        if (version_compare(phpversion('redis'), '5.3.0', '>=') && !is_null($context = carr::get($config, 'context'))) {
            $parameters[] = $context;
        }
        $client->{($persistent ? 'pconnect' : 'connect')}(...$parameters);
    }

    /**
     * Create a new redis cluster instance.
     *
     * @param array $servers
     * @param array $options
     *
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

        if (version_compare(phpversion('redis'), '5.3.2', '>=') && !is_null($context = carr::get($options, 'context'))) {
            $parameters[] = $context;
        }

        return c::tap(new RedisCluster(...$parameters), function ($client) use ($options) {
            if (!empty($options['prefix'])) {
                $client->setOption(Redis::OPT_PREFIX, $options['prefix']);
            }

            if (!empty($options['scan'])) {
                $client->setOption(Redis::OPT_SCAN, $options['scan']);
            }

            if (!empty($options['failover'])) {
                $client->setOption(RedisCluster::OPT_SLAVE_FAILOVER, $options['failover']);
            }

            if (!empty($options['name'])) {
                $client->client('SETNAME', $options['name']);
            }

            if (array_key_exists('serializer', $options)) {
                $client->setOption(Redis::OPT_SERIALIZER, $options['serializer']);
            }

            if (array_key_exists('compression', $options)) {
                $client->setOption(Redis::OPT_COMPRESSION, $options['compression']);
            }

            if (array_key_exists('compression_level', $options)) {
                $client->setOption(Redis::OPT_COMPRESSION_LEVEL, $options['compression_level']);
            }
        });
    }

    /**
     * Format the host using the scheme if available.
     *
     * @param array $options
     *
     * @return string
     */
    protected function formatHost(array $options) {
        if (isset($options['scheme'])) {
            return cstr::start($options['host'], "{$options['scheme']}://");
        }

        return $options['host'];
    }
}
