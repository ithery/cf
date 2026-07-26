<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_RadioList extends CElement_FormInput {
    /**
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->tag = 'div';
        $this->addClass('checkbox-list');
    }

    /**
     * Builds one `CElement_FormInput_Radio` child control per `$this->list`
     * entry, checking the one matching `$this->value`.
     *
     * @return void
     */
    protected function build() {
        parent::build();
        foreach ($this->list as $key => $value) {
            $controlName = $this->name ?: $this->id;
            $radioControl = $this->addRadioControl()->setName($controlName)->setValue($key)->setLabel($value);
            if ($key == $this->value) {
                $radioControl->setChecked();
            }
        }
    }
}
