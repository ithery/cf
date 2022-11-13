<?php

class CElement_Component_Action_Link extends CElement_Element_A implements CElement_Contract_ActionableInterface {
    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    public function __construct() {
        $this->classes = [
            'btn',
            'btn-link'
        ];
    }

    protected function build() {
        parent::build();
    }
}
