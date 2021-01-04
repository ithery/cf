<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Date {
    /**
     * @param type $placeholder
     *
     * @return type
     *
     * @deprecated since version 1.2
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

    /**
     * @param type  $placeholder
     * @param mixed $str
     *
     * @return type
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
        $this->end_date = $str;
        return $this;
    }

    /**
     * @param mixed $day
     *
     * @deprecated since version 1.2
     */
    public function add_disable_day($day) {
        $day_array = explode(',', $day);
        if (count($day_array) > 1) {
            foreach ($day_array as $d) {
                $this->disable_day[] = trim($d);
            }
        } else {
            $this->disable_day[] = $day;
        }
        return $this;
    }
}
//@codingStandardsIgnoreEnd
