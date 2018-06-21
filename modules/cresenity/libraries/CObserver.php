<?php

class CObserver {

    private static $_instance;
    private $obj_list;
    private $autoid;

    private function __construct() {

        $this->obj_list = array();
        $this->autoid = 0;
    }

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new CObserver();
        }
        return self::$_instance;
    }

    public static function objects() {
        return $this->obj_list;
    }

    public function newId() {
        $uniqid = uniqid(time(), true);
        $uniqid = str_replace(".", "", $uniqid);
        return $uniqid;
    }

    public function remove(CObject $obj) {
        if (array_key_exists($obj->id(), $this->obj_list)) {
            unset($this->obj_list[$obj->id()]);
            return true;
        }
        return false;
    }

    public function add(CObject $obj) {

        if (array_key_exists($obj->id(), $this->obj_list)) {
            throw new CException("Object :object_id is exists.", array(':object_id' => $obj->id()));
        }

        $this->obj_list[$obj->id()] = $obj;
    }

}
