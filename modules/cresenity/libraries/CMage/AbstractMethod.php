<?php

abstract class CMage_AbstractMethod implements CMage_MethodInterface {
    /**
     * @var CMage_AbstractMage
     */
    protected $mage;

    protected $controllerClass;

    protected $id;

    public function __construct(CMage_AbstractMage $mage, $controllerClass) {
        $this->mage = $mage;
        $this->controllerClass = $controllerClass;
    }

    public function controllerUrl() {
        return forward_static_call([$this->controllerClass, 'controllerUrl']);
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
}
