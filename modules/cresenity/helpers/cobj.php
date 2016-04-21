<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Dec 5, 2014
     * @license http://piposystem.com Piposystem
     */

    class cobj {
        
        public static function get($object, $key, $default = NULL){
            return isset($object->$key) ? $object->$key : $default;
        }
        
        public static function xml_to_string($object){
            return trim($object->__toString());
        }
        
        public static function xml_get($object, $key, $toString = 0){
            $obj = cobj::get($object, $key);
            if ($toString) {
                $obj = ($obj != NULL) ? $obj->__toString() : NULL;
            }
            return $obj;
        }
        
        public static function xml_set_key($object, $key1, $key2 = NULL){
            $obj = cobj::xml_get($object, $key1);
            if ($obj != NULL) {
                if ($key2 == NULL) {
                    $obj[] = $obj;
//                    if ($key1 == "BookingInfo") {
//                        cdbg::var_dump($obj);
//                        echo $key1;
//                        cdbg::var_dump($obj);
//                    }
                    return $obj;
                }
                else {
                    $obj_2 = cobj::xml_get($obj, $key2);
                    if ($obj_2 == NULL) $obj_2[] = $obj;
                    return $obj_2;
                }
            }
            return NULL;
        }
    }