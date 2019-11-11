<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

interface CRedis_ConnectorInterface {

    /**
     * Create a connection to a Redis cluster.
     *
     * @param  array  $config
     * @param  array  $options
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connect(array $config, array $options);

    /**
     * Create a connection to a Redis instance.
     *
     * @param  array  $config
     * @param  array  $clusterOptions
     * @param  array  $options
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connectToCluster(array $config, array $clusterOptions, array $options);
}
