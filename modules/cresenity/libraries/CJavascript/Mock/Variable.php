<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 3, 2018, 3:15:46 AM
 */
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

    public function __toString() {
        return $this->getScript();
    }
}
