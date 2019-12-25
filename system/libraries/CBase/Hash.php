<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CBase_Hash implements CBase_CacheInterface {

    use CBase_Trait_CacheDataTrait;

    public function __construct() {
        $this->clear();
    }

    public function set($key, $value) {
        $this->size += $this->has($key) ? 0 : 1;
        $this->__data__[$key] = $value;
        return $this;
    }

    public function get($key) {
        return isset($this->__data__[$key]) ? $this->__data__[$key] : null;
    }

    public function has($key) {
        return \array_key_exists($key, $this->__data__);
    }

    public function clear() {
        $this->__data__ = [];
        $this->size = 0;
    }

    public function delete($key) {
        $result = $this->has($key);
        unset($this->__data__[$key]);
        $this->size -= $result ? 1 : 0;
        return $result;
    }

}
