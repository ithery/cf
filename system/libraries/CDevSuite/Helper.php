<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Helper {

    /**
     * Search and replace using associative array
     *
     * @param array  $searchAndReplace
     * @param string $subject
     * @return string
     */
    public static function strArrayReplace($searchAndReplace, $subject) {
        return str_replace(array_keys($searchAndReplace), array_values($searchAndReplace), $subject);
    }

    /**
     * Retry the given function N times.
     *
     * @param int      $retries
     * @param callable $retries
     * @param int      $sleep
     * @return mixed
     */
    public static function retry($retries, $fn, $sleep = 0) {
        beginning:
        try {
            return $fn();
        } catch (Exception $e) {
            if (!$retries) {
                throw $e;
            }

            $retries--;

            if ($sleep > 0) {
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }

    /**
     * Resolve the given class from the container.
     *
     * @param string $class
     * @return mixed
     */
    public static function resolve($class) {
        return CContainer_Container::getInstance()->make($class);
    }

    /**
     * Swap the given class implementation in the container.
     *
     * @param string $class
     * @param mixed  $instance
     * @return void
     */
    public static function swap($class, $instance) {
        CContainer_Container::getInstance()->instance($class, $instance);
    }

}
