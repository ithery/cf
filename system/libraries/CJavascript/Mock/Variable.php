<?php

defined('SYSPATH') or die('No direct access allowed.');

class CJavascript_Mock_Variable {
    protected $propStack = [];

    protected $varName = null;

    public function __construct($varName) {
        $this->varName = $varName;
    }

    public function __get($name) {
        $cloned = clone $this;

        return $cloned->addProp($name);
    }

    public function addProp($name) {
        $this->propStack[] = $name;

        return $this;
    }

    public function getScript() {
        $var = $this->varName;
        foreach ($this->propStack as $prop) {
            $var .= '.' . $prop;
        }

        return $var;
    }

    #[\ReturnTypeWillChange]
    public function __toString() {
        return $this->getScript();
    }
}
