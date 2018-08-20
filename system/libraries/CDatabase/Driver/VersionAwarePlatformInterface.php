<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:24:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CDatabase_Driver_VersionAwarePlatformInterface {

    /**
     * Factory method for creating the appropriate platform instance for the given version.
     *
     * @param string $version The platform/server version string to evaluate. This should be given in the notation
     *                        the underlying database vendor uses.
     *
     * @return CDatabase_Platform
     *
     * @throws CDatabase_Exception if the given version string could not be evaluated.
     */
    public function createDatabasePlatformForVersion($version);
}
