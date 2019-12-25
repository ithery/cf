<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @property array $__data__
 * @property int   $size
 */
final class CBase_MapCache implements CBase_CacheInterface {

    use CBase_Trait_CacheDataTrait;

    public function __construct(iterable $entries = null) {
        $this->clear();
        if (null !== $entries) {
            foreach ($entries as $key => $entry) {
                $this->set($key, $entry);
            }
        }
    }

    final public function set($key, $value) {
        $data = $this->getMapData($key);
        $size = $data->getSize();
        $data->set($key, $value);
        $this->size += $data->getSize() === $size ? 0 : 1;
        return $this;
    }

    final public function get($key) {
        return $this->getMapData($key)->get($key);
    }

    final public function has($key) {
        return $this->getMapData($key)->has($key);
    }

    final public function clear() {
        $this->size = 0;
        $this->__data__ = [
            'hash' => new CBase_Hash,
            'map' => new CBase_ListCache,
            'string' => new CBase_Hash,
        ];
    }

    final public function delete($key) {
        $result = $this->getMapData($key)->delete($key);
        $this->size -= $result ? 1 : 0;
        return $result;
    }

    private function isKey($key) {
        return \is_scalar($key);
    }

    private function getMapData($key) {
        if ($this->isKey($key)) {
            return $this->__data__[\is_string($key) ? 'string' : 'hash'];
        }
        return $this->__data__['map'];
    }

}
