<?php

trait CDevSuite_Trait_MacTrait {
    /**
     * @var CDevSuite_Brew
     */
    protected static $brew;

    protected $macDevSuiteBin = '/usr/local/bin/devsuite';

    /**
     * @return CDevSuite_Brew
     */
    public static function brew() {
        if (static::$brew == null) {
            static::$brew = new CDevSuite_Brew();
        }

        return static::$brew;
    }
}
