<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_ObjectPrybar {
    protected $obj;

    /**
     * @var ReflectionClass
     */
    protected $reflected;

    public function __construct($obj) {
        $this->obj = $obj;
        $this->reflected = new ReflectionClass($obj);
    }

    public function getProperty($name) {
        $property = $this->reflected->getProperty($name);

        $property->setAccessible(true);

        return $property->getValue($this->obj);
    }

    public function setProperty($name, $value) {
        $property = $this->reflected->getProperty($name);

        $property->setAccessible(true);

        $property->setValue($this->obj, $value);
    }
}
