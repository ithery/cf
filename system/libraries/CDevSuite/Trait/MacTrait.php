<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 15, 2020 
 * @license Ittron Global Teknologi
 */

trait CDevSuite_Trait_MacTrait {
     /**
     *
     * @var CDevSuite_Brew
     */
    protected static $brew;

    /**
     * 
     * @return CDevSuite_Brew
     */
    public static function linuxRequirements() {
        if (static::$brew == null) {
            static::$brew = new CDevSuite_Brew();
        }
        return static::$brew;
    }

}