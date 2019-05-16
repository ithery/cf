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

    public static function arrayAll() {
        return array(self::ALL => self::ALL);
    }

    public static function arrayNone() {
        return array(self::NONE => self::NONE);
    }

}
