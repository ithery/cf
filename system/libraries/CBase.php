<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CBase {

    public static function createRecursionContext() {
        return new CBase_RecursionContext();
    }

    public static function createMemoizeResolver(callable $func, callable $resolver = null) {
        return new CBase_MemoizeResolver($func, $resolver);
    }

    public static function createMapCache() {
        return new CBase_MapCache();
    }

    

    
    

}
