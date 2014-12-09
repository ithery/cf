<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Dec 5, 2014
     * @license http://piposystem.com Piposystem
     */

    class cobj {
        
        public static function get($object, $key){
            return isset($object->$key) ? $object->$key : NULL;
        }
        
        public static function xml_get($object, $key, $toString = 0){
            $obj = cobj::get($object, $key);
            if ($toString) {
                $obj = ($obj != NULL) ? $obj->__toString() : NULL;
            }
            return $obj;
        }
        
        public static function xml_set_key($object, $key1, $key2){
            $obj = cobj::xml_get($object, $key1);
            if ($obj != NULL) {
                $obj_2 = cobj::xml_get($obj, $key2);
                if ($obj_2 == NULL) $obj_2[] = $obj;
                return $obj_2;
            }
            return NULL;
        }
    }