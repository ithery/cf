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

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    public static function classBasename($class) {
        $class = is_object($class) ? get_class($class) : $class;

        $basename = basename(str_replace('\\', '/', $class));
        $basename = carr::last(explode("_", $basename));
        return $basename;
    }

    
    /**
     * Create a collection from the given value.
     *
     * @param  mixed  $value
     * @return CCollection
     */
    public static function collect($value = null) {
        return new CCollection($value);
    }
    
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @return mixed
     */
    public static function tap($value, $callback = null) {
        if (is_null($callback)) {
            return new CBase_HigherOrderTapProxy($value);
        }

        $callback($value);

        return $value;
    }

}
