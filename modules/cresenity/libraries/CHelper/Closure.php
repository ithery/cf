<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2019, 12:42:11 AM
 */
use SuperClosure\SerializableClosure;

class CHelper_Closure {
    public static function serializeClosure($callback) {
        if ($callback instanceof \Closure) {
            $callback = new SuperClosure\SerializableClosure($callback);
        }
        return $callback;
    }

    public static function deserializeClosure($callback) {
        if (is_string($callback)) {
            try {
                $serializer = new SuperClosure\Serializer;
                $callback = $serializer->unserialize($callback);
            } catch (Exception $ex) {
                //do nothing
            }
        }
        return $callback;
    }
}
