<?php

class CDaemon_Supervisor_Queue_RedisConnector extends CQueue_Connector_RedisConnector {
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \CDaemon_Supervisor_Queue_RedisQueue
     */
    public function connect(array $config) {
        return new CDaemon_Supervisor_Queue_RedisQueue(
            $this->redis,
            $config['queue'],
            carr::get($config, 'connection', $this->connection),
            carr::get($config, 'retry_after', 60),
            carr::get($config, 'block_for', null),
            carr::get($config, 'after_commit', null)
        );
    }
}
