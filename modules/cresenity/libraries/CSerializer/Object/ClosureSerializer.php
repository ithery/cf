<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 23, 2018, 12:09:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use SuperClosure\SerializableClosure;

class CSerializer_Object_ClosureSerializer {

    /**
     * @param CSerializer_Serializer    $serializer
     * @param Closure $splFixedArray
     *
     * @return array
     */
    public static function serialize(CSerializer_Serializer $serializer, Closure $closure) {
        $closureSerializer = new Serializer();
        $serialized = $closureSerializer->serialize($closure);
        return $serialized;
    }

    /**
     * @param CSerializer_Serializer $serializer
     * @param string     $param
     *
     * @return mixed
     */
    public static function unserialize(CSerializer_Serializer $serializer, $param) {
        $closureSerializer = new Serializer();
        $unserialized = $closureSerializer->unserialize($param);
        return $serialized;
    }

}
