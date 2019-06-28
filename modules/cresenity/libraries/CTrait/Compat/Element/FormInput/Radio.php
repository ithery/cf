<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 1:55:42 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Radio {

    public function set_applyjs($applyjs) {
        return $this->setApplyJs($applyjs);
    }

    public function set_checked($bool) {
        return $this->setChecked($bool);
    }

    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

}
