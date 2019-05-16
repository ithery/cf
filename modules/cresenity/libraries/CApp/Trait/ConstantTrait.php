<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 3:02:59 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_ConstantTrait {

    public static function yesNoList() {
        return array(self::NO_LABEL, self::YES_LABEL);
    }

}
