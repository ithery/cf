<?php

class CEmail_Builder_Component_HeadComponent_Head extends CEmail_Builder_Component_HeadComponent {
    protected static $tagName = 'c-head';

    public function handler() {
        return $this->handlerChildren();
    }
}
