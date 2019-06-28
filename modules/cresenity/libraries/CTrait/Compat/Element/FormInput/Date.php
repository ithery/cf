<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 5:15:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Date {

    /**
     * 
     * @deprecated since version 1.2
     * @param type $placeholder
     * @return type
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @param type $placeholder
     * @return type
     */
    public function set_start_date($str) {
    	return $this->setStartDate($str);
    }

}
