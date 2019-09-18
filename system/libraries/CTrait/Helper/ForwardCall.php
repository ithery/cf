<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 5:13:50 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Helper_ForwardCall {

    /**
     * Forward a method call to the given object.
     *
     * @param  mixed  $object
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    protected function forwardCallTo($object, $method, $parameters) {
        try {
            return $object->{$method}(...$parameters);
        } catch (Exception $e) {
            $pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';
            if (!preg_match($pattern, $e->getMessage(), $matches)) {
                throw $e;
            }
            if ($matches['class'] != get_class($object) ||
                    $matches['method'] != $method) {
                throw $e;
            }
            static::throwBadMethodCallException($method);
        }
    }

    /**
     * Throw a bad method call exception for the given method.
     *
     * @param  string  $method
     * @return void
     *
     * @throws \BadMethodCallException
     */
    protected static function throwBadMethodCallException($method) {
        throw new BadMethodCallException(sprintf(
                'Call to undefined method %s::%s()', static::class, $method
        ));
    }

}
