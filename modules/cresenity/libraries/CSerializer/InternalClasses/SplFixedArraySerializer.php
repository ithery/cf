<?php

class SplFixedArraySerializer {
    /**
     * @param CSerializer_Serializer $serializer
     * @param SplFixedArray          $splFixedArray
     *
     * @return array
     */
    public static function serialize(CSerializer_Serializer $serializer, SplFixedArray $splFixedArray) {
        $toArray = [
            CSerializer_Serializer::CLASS_IDENTIFIER_KEY => get_class($splFixedArray),
            CSerializer_Serializer::CLASS_PARENT_KEY => 'SplFixedArray',
            CSerializer_Serializer::SCALAR_VALUE => [],
        ];
        foreach ($splFixedArray->toArray() as $key => $field) {
            $toArray[CSerializer_Serializer::SCALAR_VALUE][$key] = $serializer->serialize($field);
        }

        return $toArray;
    }

    /**
     * @param CSerializer_Serializer $serializer
     * @param string                 $className
     * @param array                  $value
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
