<?php

class CQueue_Queue_AsyncRedisQueue extends CQueue_Queue_RedisQueue {
    /**
     * Get the connection for the queue.
     *
     * @return \BeyondCode\LaravelWebSockets\Contracts\ChannelManager|\Illuminate\Redis\Connections\Connection
     */
    public function getConnection() {
        $channelManager = $this->container->bound(ChannelManager::class)
            ? $this->container->make(ChannelManager::class)
            : null;

        return $channelManager && method_exists($channelManager, 'getRedisClient')
            ? $channelManager->getRedisClient()
            : parent::getConnection();
    }
}
