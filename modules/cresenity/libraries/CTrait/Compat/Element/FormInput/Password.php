<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 2:37:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Password {

    /**
     * 
     * @deprecated, please use setPlaceholder
     * @param type $placeholder
     * @return type
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

}
