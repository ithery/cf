<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 6:08:17 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Currency {

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