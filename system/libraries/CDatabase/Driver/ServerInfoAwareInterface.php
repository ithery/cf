<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:40:07 AM
 */
interface CDatabase_Driver_ServerInfoAwareInterface {
    /**
     * Returns the version number of the database server connected to.
     *
     * @return string
     */
    public function getServerVersion();

    /**
     * Checks whether a query is required to retrieve the database server version.
     *
     * @return bool true if a query is required to retrieve the database server version, false otherwise
     */
    public function requiresQueryForServerVersion();
}
