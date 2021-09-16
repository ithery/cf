<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 17, 2019, 3:02:59 AM
 */
trait CApp_Trait_ConstantTrait {
    public static function yesNoList() {
        return [self::NO_LABEL, self::YES_LABEL];
    }

    public static function arrayAll() {
        return [self::ALL => self::ALL];
    }

    public static function arrayNone() {
        return [self::NONE => self::NONE];
    }
}
