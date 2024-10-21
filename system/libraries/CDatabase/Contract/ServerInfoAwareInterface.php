<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CDatabase_Contract_ServerInfoAwareInterface {
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
