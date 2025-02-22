<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Date {
    /**
     * @param string $placeholder
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

    /**
     * @param string $placeholder
     * @param mixed  $str
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_start_date($str) {
        return $this->setStartDate($str);
    }

    /**
     * @param mixed $boolean
     *
     * @deprecated since version 1.2
     */
    public function set_have_button($boolean) {
        $this->have_button = $boolean;

        return $this;
    }

    /**
     * @param mixed $str
     *
     * @deprecated since version 1.2
     */
    public function set_end_date($str) {
        return $this->setEndDate($str);
    }

    /**
     * @param mixed $day
     *
     * @deprecated since version 1.2
     */
    public function add_disable_day($day) {
        return $this->addDisableDaysOfWeek($day);
    }
}
//@codingStandardsIgnoreEnd
