<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:22:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CColor_Format_Type_Hex extends CColor_Format_TypeAbstract {

    public function __toString() {
        return $this->value;
    }

}
