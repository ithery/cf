<?php

abstract class CElement_Component_Chart_EngineAbstract {

    protected $data;
    public function __construct() {
        $this->data = [];
    }

    public function getColor($color = null, $opacity = 1.0) {
        if (!$color) {
            return 'rgba(' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . $opacity . ')';
        } else {
            preg_match_all("([\d\.]+)", $color, $matches);
            $opacity = $opacity ?: $matches[0][3];

            return 'rgba(' . $matches[0][0] . ', ' . $matches[0][1] . ', ' . $matches[0][2] . ', ' . $opacity . ')';
        }
    }
    /**
     * @return Closure
     */
    abstract public function getBuildElementCallback();
    /**
     * @return string
     */
    abstract public function js(CElement_Component_Chart $element, $indent = 0);
}
