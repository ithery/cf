<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_FormInput_Time
 */
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
        /** @var CElement_FormInput_Time $this */
        return $this->setPlaceholder($placeholder);
    }

    /**
     * Sets an undefined dynamic property -- CElement_FormInput_Time has no
     * $show_meridian property, so this has no observable effect.
     *
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_show_meridian($bool) {
        /** @var CElement_FormInput_Time $this */
        $this->show_meridian = $bool;

        return $this;
    }

    /**
     * Sets an undefined dynamic property -- CElement_FormInput_Time has no
     * $show_second property, so this has no observable effect.
     *
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_show_second($bool) {
        /** @var CElement_FormInput_Time $this */
        $this->show_second = $bool;

        return $this;
    }

    /**
     * Sets an undefined dynamic property -- CElement_FormInput_Time has no
     * $minute_step property, so this has no observable effect.
     *
     * @param int $step
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_minute_step($step) {
        /** @var CElement_FormInput_Time $this */
        $this->minute_step = $step;

        return $this;
    }
}
//@codingStandardsIgnoreEnd
