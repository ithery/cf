<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

interface CBase_CacheInterface {

    public function set($key, $value);

    public function get($key);

    public function has($key);

    public function clear();

    public function delete($key);

    public function getSize();
}
