<?php

/**
 * A wrapper-less element used to hold before()/after() child content, rendering
 * only its children's HTML/JS without its own tag.
 */
class CElement_PseudoElement extends CElement_Element {
    /**
     * @param string $id
     * @param string $tag
     *
     * @return \CElement_PseudoElement
     */
    public static function factory($id = '', $tag = 'div') {
        return new CElement_PseudoElement($id, $tag);
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        return parent::htmlChild();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        return parent::jsChild();
    }
}
