<?php

class CQueue_Queue_AsyncRedisQueue extends CQueue_Queue_RedisQueue {
    /**
     * Get the connection for the queue.
     *
     * @return \CWebSocket_Contract_ChannelManagerInterface|\CRedis_AbstractConnection
     */
    public function getConnection() {
        $channelManager = $this->container->bound(CWebSocket_Contract_ChannelManagerInterface::class)
            ? $this->container->make(CWebSocket_Contract_ChannelManagerInterface::class)
            : null;

        return $channelManager && method_exists($channelManager, 'getRedisClient')
            ? $channelManager->getRedisClient()
            : parent::getConnection();
    }
}
