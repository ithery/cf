<?php

interface CDatabase_Contract_ConnectionResolverInterface {
    /**
     * Get a database connection instance.
     *
     * @param null|string $name
     *
     * @return \CDatabase_ConnectionInterface
     */
    public function connection($name = null);

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection();

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultConnection($name);
}
