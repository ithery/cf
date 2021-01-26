<?php

interface CBase_CacheInterface {
    public function set($key, $value);

    public function get($key);

    public function has($key);

    public function clear();

    public function delete($key);

    public function getSize();
}
