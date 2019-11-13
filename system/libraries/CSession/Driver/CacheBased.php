<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CSession_Driver_CacheBased implements CSession_Driver {

    /**
     * The cache repository instance.
     *
     * @var CCache_Repository
     */
    protected $cache;

    /**
     * The number of seconds to store the data in the cache.
     *
     * @var int
     */
    protected $seconds;

    /**
     * Create a new cache driven handler instance.
     *
     * @param  CCache_Repository  $cache
     * @param  int  $seconds
     * @return void
     */
    public function __construct(CCache_Repository $cache, $seconds) {
        $this->cache = $cache;
        $this->seconds = $seconds;
    }

    public function close() {
        return true;
    }

    public function destroy($id) {
        return $this->cache->forget($id);
    }

    public function gc($maxlifetime) {
        return true;
    }

    public function open($path, $name) {
        return true;
    }

    public function read($id) {
        return $this->cache->get($id, '');
    }

    public function regenerate() {
        
    }

    public function write($id, $data) {
        return $this->cache->put($id, $data, $this->seconds);
    }

    /**
     * Get the underlying cache repository.
     *
     * @return CCache_Repository
     */
    public function getCache() {
        return $this->cache;
    }

}
