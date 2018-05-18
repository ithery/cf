<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 2:29:43 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput {

    /**
     * @deprecated since version 1.2
     */
    public function set_value($val) {
        return $this->setValue($val);
    }

}
