<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_CheckboxList extends CElement_FormInput {
    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->tag = 'div';
        $this->addClass('checkbox-list');
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();
        foreach ($this->list as $key => $value) {
            $controlName = $this->name ?: $this->id;
            $this->addCheckboxControl()->setName($controlName . '[' . $key . ']')->setValue(1)->setLabel($value);
        }
    }
}
