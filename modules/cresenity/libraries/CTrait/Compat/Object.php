<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 3:42:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Object {

    /**
     * 
     * @deprecated since version 1.2
     * @return string
     */
    public function regenerate_id() {
        return $this->regenerateId();
    }

}
