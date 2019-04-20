<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 3:37:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Handler_Driver_Submit {

    /**
     * 
     * @deprecated, please use setForm
     * @param string $formId
     * @return $this
     */
    public function set_form($formId) {
        return $this->setForm($formId);
    }

}
