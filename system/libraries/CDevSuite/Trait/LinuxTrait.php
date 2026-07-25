<?php

/**
 * Description of LinuxTrait
 */
trait CDevSuite_Trait_LinuxTrait {
    /**
     * @var CDevSuite_LinuxRequirements
     */
    protected static $linuxRequirements;

    /**
     * Get the LinuxRequirements instance, creating it if necessary.
     *
     * @return CDevSuite_LinuxRequirements
     */
    public static function linuxRequirements() {
        if (static::$linuxRequirements == null) {
            static::$linuxRequirements = new CDevSuite_LinuxRequirements();
        }
        return static::$linuxRequirements;
    }
}
