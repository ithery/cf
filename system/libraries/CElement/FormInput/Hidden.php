<?php

class CElement_FormInput_Hidden extends CElement_FormInput {
    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'hidden';
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
    }
}
