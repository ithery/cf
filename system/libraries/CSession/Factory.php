<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CSession_Factory {

    /**
     * Create an instance of the Redis session driver.
     *
     * @return CSession
     */
    public static function createRedisDriver() {
        $cacheOptions = array();
        $cacheOptions['driver'] = 'Redis';
        
        $redis = CRedis::instance(CF::config('session.storage'));
        $driver = new CCache_Driver_RedisDriver($redis);
        $redisStore = new CCache_Repository($driver);
        $expirationSeconds = CF::config('session.expiration');
        $handler = new CSession_Driver_Redis($redisStore, $expirationSeconds);
        return $handler;
    }

}
