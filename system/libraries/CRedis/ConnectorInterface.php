<?php

interface CRedis_ConnectorInterface {
    /**
     * Create a connection to a Redis cluster.
     *
     * @param array $config
     * @param array $options
     *
     * @return CRedis_ConnectionInterface
     */
    public function connect(array $config, array $options);

    /**
     * Create a connection to a Redis instance.
     *
     * @param array $config
     * @param array $clusterOptions
     * @param array $options
     *
     * @return CRedis_ConnectionInterface
     */
    public function connectToCluster(array $config, array $clusterOptions, array $options);
}
