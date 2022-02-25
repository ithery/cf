<?php

class CElement_FormInput_Label extends CElement_FormInput {
    use CTrait_Element_Property_DependsOn;

    public function __construct($id) {
        parent::__construct($id);

        $this->tag = 'span';
        $this->type = 'label';
        $this->isOneTag = false;
    }

    public static function factory($id = '') {
        return CElement_Factory::create(static::class, $id);
    }

    protected function build() {
        $value = $this->value;
        if ($value instanceof CRenderable) {
            $this->value = $value->html();
            $this->addJs($value->js());
        }
        $this->addJs($this->getDependsOnContentJavascript());
        $this->addClass('label');
        $this->setAttr('name', $this->name);
        parent::build();

        $this->setAttr('value', '');
        $this->add($this->value);
    }
}
