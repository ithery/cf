<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 2:00:52 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Email extends CElement_FormInput {

    use CTrait_Element_Property_Placeholder;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "email";
        $this->placeholder = "";
        $this->addClass('form-control');
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        
        $this->setAttr('placeholder', $this->placeholder);

        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
    }

}
