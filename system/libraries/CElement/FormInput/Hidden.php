<?php

class CElement_FormInput_Hidden extends CElement_FormInput {
    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'hidden';
    }

    public static function factory($id) {
        return new static($id);
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        $this->setAttr('name', $this->name);
    }
}
