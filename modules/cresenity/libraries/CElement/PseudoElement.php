<?php

class CElement_PseudoElement extends CElement_Element {
    public static function factory($id = '', $tag = 'div') {
        return new CElement_PseudoElement($id, $tag);
    }

    public function html($indent = 0) {
        return parent::htmlChild();
    }

    public function js($indent = 0) {
        return parent::jsChild();
    }
}
