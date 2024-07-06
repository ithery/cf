<?php

class CReport_Jasper_Element_Root extends CReport_Jasper_Element {
    public function generate($obj = null) {
        $row = is_array($obj) ? $obj[1] : [];
        $obj = is_array($obj) ? $obj[0] : $obj;
        if ($this->children) {
            foreach ($this->children as $child) {
                // se for objeto
                if (is_object($child)) {
                    $child->generate([$obj, $row]);
                }
            }
        }
    }
}
