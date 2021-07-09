<?php

/**
 * Description of LinuxTrait
 *
 * @author Hery
 */
trait CDevSuite_Trait_LinuxTrait {
    /**
     * @var CDevSuite_LinuxRequirements
     */
    protected static $linuxRequirements;

    /**
     * @return CDevSuite_LinuxRequirements
     */
    public static function linuxRequirements() {
        if (static::$linuxRequirements == null) {
            static::$linuxRequirements = new CDevSuite_LinuxRequirements();
        }
        return static::$linuxRequirements;
    }
}
