<?php

/**
 * Description of MacTrait.
 */
trait CDevSuite_Trait_MacTrait {
    /**
     * @var CDevSuite_Brew
     */
    protected static $brew;

    /**
     * @var string
     */
    protected $macDevSuiteBin = '/usr/local/bin/devsuite';

    /**
     * Get the Brew instance, creating it if necessary.
     *
     * @return CDevSuite_Brew
     */
    public static function brew() {
        if (static::$brew == null) {
            static::$brew = new CDevSuite_Brew();
        }

        return static::$brew;
    }
}
