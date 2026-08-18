<?php

/**
 * Description of PackageManager.
 */
abstract class CDevSuite_PackageManager {
    /**
     * Determine if the given package is installed.
     *
     * @param string $package
     *
     * @return bool
     */
    abstract public function installed($package);

    /**
     * Ensure the given package is installed, installing it if necessary.
     *
     * @param string $package
     *
     * @return void
     */
    abstract public function ensureInstalled($package);

    /**
     * Restart the system's Network Manager, if present.
     *
     * @param CDevSuite_ServiceManager $sm
     *
     * @return void
     */
    abstract public function nmRestart($sm);
}
