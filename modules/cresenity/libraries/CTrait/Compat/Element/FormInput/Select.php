<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 9:18:22 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Select {

    /**
     * @deprecated since version 1.2
     */
    public function set_list($list) {
        return $this->setList($list);
    }

}
