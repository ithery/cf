<?php

class CRedis_Connection_PredisClusterConnection extends CRedis_Connection_PredisConnection {
    /**
     * Flush the selected Redis database on all cluster nodes.
     *
     * @return void
     */
    public function flushdb() {
        $command = class_exists(ServerFlushDatabase::class)
            ? ServerFlushDatabase::class
            : FLUSHDB::class;

        foreach ($this->client as $node) {
            $node->executeCommand(c::tap(new $command())->setArguments(func_get_args()));
        }
    }
}
