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
     * The number of minutes to store the data in the cache.
     *
     * @var int
     */
    protected $minutes;

    /**
     * Create a new cache driven handler instance.
     *
     * @param  CCache_Repository  $cache
     * @param  int  $minutes
     * @return void
     */
    public function __construct(CCache_Repository $cache, $minutes) {
        $this->cache = $cache;
        $this->minutes = $minutes;
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
        return $this->cache->put($id, $data, $this->minutes * 60);
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
