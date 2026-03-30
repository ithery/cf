<?php

defined('SYSPATH') or die('No direct access allowed.');

 //@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Time {
    /**
     * @deprecated since version 1.2
     *
     * @param string $placeholder
     *
     * @return CElement_FormInput_Time
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

    public function set_show_meridian($bool) {
        $this->show_meridian = $bool;

        return $this;
    }

    public function set_show_second($bool) {
        $this->show_second = $bool;

        return $this;
    }

    public function set_minute_step($step) {
        $this->minute_step = $step;

        return $this;
    }
}
//@codingStandardsIgnoreEnd
