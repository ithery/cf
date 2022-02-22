<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 2:29:43 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Form_FieldValidation {
    /**
     * @param string $name
     * @param mixed  $param
     *
     * @deprecated 1.2 use addValidation
     *
     * @return $this
     */
    public function add_validation($name, $param) {
        return $this->addValidation($name, $param);
    }

    /**
     * @param mixed $input
     *
     * @return $this
     *
     * @deprecated 1.2 dont use this anymore
     */
    public function condrequired($input) {
        $this->validation['condrequired'] = $input;

        return $this;
    }

    /**
     * @return string
     *
     * @deprecated 1.2
     */
    public function validation_class() {
        return $this->validationClass();
    }
}
