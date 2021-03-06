<?php

use Predis\Client;

class CRedis_Connector_PredisConnector extends CRedis_AbstractConnector {
    /**
     * Create a new clustered Predis connection.
     *
     * @param array $config
     * @param array $options
     *
     * @return \CRedis_Connection_PredisConnection
     */
    public function connect(array $config, array $options) {
        $formattedOptions = array_merge(
            ['timeout' => 10.0],
            $options,
            carr::pull($config, 'options', [])
        );

        return new CRedis_Connection_PredisConnection(new Client($config, $formattedOptions));
    }

    /**
     * Create a new clustered Predis connection.
     *
     * @param array $config
     * @param array $clusterOptions
     * @param array $options
     *
     * @return \CRedis_Connection_PredisClusterConnection
     */
    public function connectToCluster(array $config, array $clusterOptions, array $options) {
        $clusterSpecificOptions = carr::pull($config, 'options', []);

        return new CRedis_Connection_PredisClusterConnection(new Client(array_values($config), array_merge(
            $options,
            $clusterOptions,
            $clusterSpecificOptions
        )));
    }
}
