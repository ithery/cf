<?php

class CQueue_Connector_AsyncRedisConnector extends CQueue_Connector_RedisConnector {
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \CQueue_QueueInterface
     */
    public function connect(array $config) {
        return new CQueue_Queue_AsyncRedisQueue(
            $this->redis,
            $config['queue'],
            isset($config['connection']) ? $config['connection'] : $this->connection,
            isset($config['retry_after']) ? $config['retry_after'] : 60,
            isset($config['block_for']) ? $config['block_for'] : null,
        );
    }
}
