<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2019, 12:42:11 AM
 */
use SuperClosure\SerializableClosure;

/**
 * @deprecated since 1.3
 */
class CHelper_Closure {
    /**
     * @param mixed $callback
     *
     * @deprecated since 1.3
     */
    public static function serializeClosure($callback) {
        if ($callback instanceof \Closure) {
            $callback = new SuperClosure\SerializableClosure($callback);
        }

        return $callback;
    }

    /**
     * @param mixed $callback
     *
     * @deprecated since 1.3
     */
    public static function deserializeClosure($callback) {
        if (is_string($callback)) {
            try {
                $serializer = new SuperClosure\Serializer();
                $callback = $serializer->unserialize($callback);
            } catch (Exception $ex) {
                //do nothing
            }
        }

        return $callback;
    }

    /**
     * @param mixed $callback
     *
     * @deprecated since 1.3
     */
    public static function serialize($callback) {
        try {
            $serializer = new SuperClosure\Serializer();
            $callback = $serializer->serialize($callback);
        } catch (Exception $ex) {
            //do nothing
        }

        return $callback;
    }

    /**
     * @param callable $callback
     *
     * @deprecated since 1.3
     */
    public static function unserialize($callback) {
        if (is_string($callback)) {
            try {
                $serializer = new SuperClosure\Serializer();
                $callback = $serializer->unserialize($callback);
            } catch (Exception $ex) {
                //do nothing
            }
        }

        return $callback;
    }
}
