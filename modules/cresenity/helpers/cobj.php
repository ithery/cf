<?php

//@codingStandardsIgnoreStart
class cobj {
    public static function get($object, $key, $default = null) {
        return isset($object->$key) ? $object->$key : $default;
    }

    public static function xml_to_string($object) {
        return trim($object->__toString());
    }

    public static function xml_get($object, $key, $toString = 0) {
        $obj = cobj::get($object, $key);
        if ($toString) {
            $obj = ($obj != null) ? $obj->__toString() : null;
        }
        return $obj;
    }

    public static function xml_set_key($object, $key1, $key2 = null) {
        $obj = cobj::xml_get($object, $key1);
        if ($obj != null) {
            if ($key2 == null) {
                $obj[] = $obj;
//                    if ($key1 == "BookingInfo") {
//                        cdbg::var_dump($obj);
//                        echo $key1;
//                        cdbg::var_dump($obj);
//                    }
                return $obj;
            } else {
                $obj_2 = cobj::xml_get($obj, $key2);
                if ($obj_2 == null) {
                    $obj_2[] = $obj;
                }
                return $obj_2;
            }
        }
        return null;
    }
}
//@codingStandardsIgnoreEnd
