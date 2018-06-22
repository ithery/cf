<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2018, 4:15:32 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Checkbox {

    /**
     * 
     * @deprecated
     * @param string $label
     * @param string $lang
     * @return $this
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

}
