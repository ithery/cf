<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 23, 2018, 12:09:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSerialzer_Object_SplFixedArraySerializer {

    /**
     * @param Serializer    $serializer
     * @param SplFixedArray $splFixedArray
     *
     * @return array
     */
    public static function serialize(CSerializer_Serializer $serializer, SplFixedArray $splFixedArray) {
        $toArray = [
            Serializer::CLASS_IDENTIFIER_KEY => get_class($splFixedArray),
            Serializer::CLASS_PARENT_KEY => 'SplFixedArray',
            Serializer::SCALAR_VALUE => [],
        ];
        foreach ($splFixedArray->toArray() as $key => $field) {
            $toArray[CSerializer_Serializer::SCALAR_VALUE][$key] = $serializer->serialize($field);
        }
        return $toArray;
    }

    /**
     * @param Serializer $serializer
     * @param string     $className
     * @param array      $value
     *
     * @return mixed
     */
    public static function unserialize(CSerializer_Serializer $serializer, $className, array $value) {
        $data = $serializer->unserialize($value[CSerializer_Serializer::SCALAR_VALUE]);
        /* @var SplFixedArray $instance */
        $ref = new ReflectionClass($className);
        $instance = $ref->newInstanceWithoutConstructor();
        $instance->setSize(count($data));
        foreach ($data as $k => $v) {
            $instance[$k] = $v;
        }
        return $instance;
    }

}
