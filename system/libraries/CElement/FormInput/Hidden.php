<?php

class CElement_FormInput_Hidden extends CElement_FormInput {
    use CTrait_Element_Property_DependsOn;
    use CTrait_Element_Transform;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'hidden';
    }

    public static function factory($id = null) {
        return new CElement_FormInput_Hidden($id);
    }

    protected function build() {
        $value = $this->value;
        $value = $this->applyTransform($value);
        $this->setAttr('type', $this->type);
        $this->setAttr('value', (string) $value);
        $this->setAttr('name', $this->name);
        $this->addJs($this->getDependsOnValueJavascript());
        parent::build();
    }
}
