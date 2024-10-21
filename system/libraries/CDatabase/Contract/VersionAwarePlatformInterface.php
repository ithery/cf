<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CDatabase_Contract_VersionAwarePlatformInterface {
    /**
     * Factory method for creating the appropriate platform instance for the given version.
     *
     * @param string $version The platform/server version string to evaluate. This should be given in the notation
     *                        the underlying database vendor uses.
     *
     * @throws CDatabase_Exception if the given version string could not be evaluated
     *
     * @return CDatabase_Platform
     */
    public function createDatabasePlatformForVersion($version);
}
