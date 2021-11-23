<?php

class CQueue_Connector_RedisConnector implements CQueue_ConnectorInterface {
    /**
     * The Redis database instance.
     *
     * @var CRedis_FactoryInterface
     */
    protected $redis;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new Redis queue connector instance.
     *
     * @param CRedis_FactoryInterface $redis
     * @param null|string             $connection
     *
     * @return void
     */
    public function __construct(CRedis_FactoryInterface $redis, $connection = null) {
        $this->redis = $redis;
        $this->connection = $connection;
    }

    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \CQueue_QueueInterface
     */
    public function connect(array $config) {
        return new CQueue_Queue_RedisQueue(
            $this->redis,
            $config['queue'],
            isset($config['connection']) ? $config['connection'] : $this->connection,
            isset($config['retry_after']) ? $config['retry_after'] : 60,
            isset($config['block_for']) ? $config['block_for'] : null,
            isset($config['after_commit']) ? $config['after_commit'] : null
        );
    }
}
