<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 30, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_ObjectPrybar {

    protected $obj;

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
