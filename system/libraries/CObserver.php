<?php

class CObserver {
    private static $instance;

    private $objectList;

    private function __construct() {
        $this->objectList = [];
        $this->autoid = 0;
    }

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function objects() {
        return $this->objectList;
    }

    public function newId() {
        $uniqid = uniqid(time(), true);
        $uniqid = str_replace('.', '', $uniqid);

        return $uniqid;
    }

    public function remove(CObject $obj) {
        if (array_key_exists($obj->id(), $this->objectList)) {
            unset($this->objectList[$obj->id()]);

            return true;
        }

        return false;
    }

    public function add(CObject $obj) {
        if (array_key_exists($obj->id(), $this->objectList)) {
            throw new CException('Object :object_id is exists.', [':object_id' => $obj->id()]);
        }

        $this->objectList[$obj->id()] = $obj;
    }
}
