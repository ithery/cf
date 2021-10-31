<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2018, 4:15:32 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Checkbox {
    /**
     * @param string $label
     * @param string $lang
     *
     * @deprecated
     *
     * @return $this
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

    /**
     * @param string $label
     * @param string $lang
     * @param mixed  $bool
     *
     * @deprecated
     *
     * @return $this
     */
    public function set_checked($bool) {
        return $this->setChecked($bool);
    }

    public function set_applyjs($applyjs) {
        $this->applyjs = $applyjs;
        return $this;
    }

    public function set_label_wrap($bool) {
        $this->label_wrap = $bool;
        return $this;
    }

    public function set_display_inline($bool) {
        $this->display_inline = $bool;
        return $this;
    }
}
