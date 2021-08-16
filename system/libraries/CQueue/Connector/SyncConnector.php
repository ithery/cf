<?php

class CQueue_Connector_SyncConnector extends CQueue_AbstractConnector {
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \CQueue_QueueInterface
     */
    public function connect(array $config) {
        return new CQueue_Queue_SyncQueue;
    }
}
