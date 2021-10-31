<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 1:55:42 AM
 */
//@codingStandardsIgnoreStart
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

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated 1.2 use setLabelWrap
     */
    public function set_label_wrap($bool) {
        return $this->setLabelWrap($bool);
    }

    public function get_inline() {
        return $this->getInline();
    }

    public function set_inline($inline) {
        return $this->setInline($inline);
    }
}
