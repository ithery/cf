<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 6:46:52 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

trait CTrait_Compat_Element_FormInput_Clock {
    
    /**
     * 
     * @deprecated since version 1.2
     * @param type $placeholder
     * @return type
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }
}