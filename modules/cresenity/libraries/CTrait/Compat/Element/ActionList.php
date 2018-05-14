<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 6:19:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_ActionList {

    /**
     * @deprecated since version 1.2
     */
    public function set_style($style) {
        return $this->setStyle($style);
    }

}
