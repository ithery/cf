<?php

class CNavigation_Nav implements CInterface_Arrayable {
    protected $name;

    protected $data;

    public function __construct($name, $data) {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName() {
        $this->name;
    }

    public function getData() {
        return $this->data;
    }
    public function toArray() {
        return $this->getData();
    }
}
