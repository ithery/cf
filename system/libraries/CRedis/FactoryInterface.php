<?php
/**
 * @see CRedis
 */
interface CRedis_FactoryInterface {
    /**
     * Get a Redis connection by name.
     *
     * @param null|string $name
     *
     * @return CRedis_ConnectionInterface
     */
    public function connection($name = null);
}
