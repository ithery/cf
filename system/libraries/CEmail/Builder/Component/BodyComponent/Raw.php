<?php

class CEmail_Builder_Component_BodyComponent_Raw extends CEmail_Builder_Component_BodyComponent {
    protected static $tagName = 'c-raw';
    protected static $endingTag = true;
    protected static $rawElement = true;

    public function render() {
        return $this->getContent();
    }
}
