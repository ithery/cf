<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 2:00:52 PM
 */
class CElement_FormInput_Range extends CElement_FormInput {
    use CTrait_Element_Property_Placeholder;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'range';
        $this->placeholder = '';
        $this->addClass('form-control form-range');
    }

    public function setMin($min) {
        $this->setAttr('min', $min);

        return $this;
    }

    public function setMax($max) {
        $this->setAttr('max', $max);

        return $this;
    }

    public function setStep($step) {
        $this->setAttr('step', $step);

        return $this;
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
