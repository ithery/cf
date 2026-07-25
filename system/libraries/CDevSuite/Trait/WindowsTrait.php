<?php

/**
 * Description of WindowsTrait
 */
trait CDevSuite_Trait_WindowsTrait {
    /**
     * @var CDevSuite_Winsw
     */
    protected static $winsw;

    /**
     * @var CDevSuite_Acrylic
     */
    protected static $acrylic;

    /**
     * Get the Winsw instance, creating it if necessary.
     *
     * @return CDevSuite_Winsw
     */
    public static function winsw() {
        if (static::$winsw == null) {
            static::$winsw = new CDevSuite_Winsw();
        }
        return static::$winsw;
    }

    /**
     * Get the Acrylic instance, creating it if necessary.
     *
     * @return CDevSuite_Acrylic
     */
    public static function acrylic() {
        if (static::$acrylic == null) {
            static::$acrylic = new CDevSuite_Acrylic();
        }
        return static::$acrylic;
    }
}
