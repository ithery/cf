<?php

interface CRedis_FactoryInterface {
    /**
     * Get a Redis connection by name.
     *
     * @param string|null $name
     *
     * @return CRedis_ConnectionInterface
     */
    public function connection($name = null);
}
