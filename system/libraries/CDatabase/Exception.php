<?php

/**
 * Database exceptions.
 */
class CDatabase_Exception extends Exception {
    /**
     * @param string $method
     *
     * @return CDatabase_Exception
     */
    public static function notSupported($method) {
        return new self("Operation '" . $method . "' is not supported by platform.");
    }

    public static function invalidPlatformSpecified() {
        return new self(
            "Invalid 'platform' option specified, need to give an instance of AbstractPlatform."
        );
    }

    /**
     * @param mixed $invalidPlatform
     */
    public static function invalidPlatformType($invalidPlatform) {
        if (\is_object($invalidPlatform)) {
            return new self(
                sprintf(
                    "Option 'platform' must be a subtype of '%s', instance of '%s' given",
                    CDatabase_Platform::class,
                    \get_class($invalidPlatform)
                )
            );
        }

        return new self(
            sprintf(
                "Option 'platform' must be an object and subtype of '%s'. Got '%s'",
                CDatabase_Platform::class,
                \gettype($invalidPlatform)
            )
        );
    }

    /**
     * Returns a new instance for an invalid specified platform version.
     *
     * @param string $version        the invalid platform version given
     * @param string $expectedFormat the expected platform version format
     *
     * @return DBALException
     */
    public static function invalidPlatformVersionSpecified($version, $expectedFormat) {
        return new self(
            sprintf(
                'Invalid platform version "%s" specified. '
                        . 'The platform version has to be specified in the format: "%s".',
                $version,
                $expectedFormat
            )
        );
    }

    /**
     * @return CDatabase_Exception
     */
    public static function invalidPdoInstance() {
        return new self(
            "The 'pdo' option was used in DriverManager::getConnection() but no "
                . 'instance of PDO was given.'
        );
    }

    /**
     * @param null|string $url the URL that was provided in the connection parameters (if any)
     *
     * @return CDatabase_Exception
     */
    public static function driverRequired($url = null) {
        if ($url) {
            return new self(
                sprintf(
                    "The options 'driver' or 'driverClass' are mandatory if a connection URL without scheme "
                            . 'is given to DriverManager::getConnection(). Given URL: %s',
                    $url
                )
            );
        }

        return new self("The options 'driver' or 'driverClass' are mandatory if no PDO "
                . 'instance is given to DriverManager::getConnection().');
    }

    /**
     * @param string $unknownDriverName
     * @param array  $knownDrivers
     *
     * @return CDatabase_Exception
     */
    public static function unknownDriver($unknownDriverName, array $knownDrivers) {
        return new self("The given 'driver' " . $unknownDriverName . ' is unknown, '
                . 'Doctrine currently supports only the following drivers: ' . implode(', ', $knownDrivers));
    }

    /**
     * Returns a human-readable representation of an array of parameters.
     * This properly handles binary data by returning a hex representation.
     *
     * @param array $params
     *
     * @return string
     */
    private static function formatParameters(array $params) {
        return '[' . implode(', ', array_map(function ($param) {
            if (is_resource($param)) {
                return (string) $param;
            }

            $json = @json_encode($param);

            if (!is_string($json) || $json == 'null' && is_string($param)) {
                // JSON encoding failed, this is not a UTF-8 string.
                return '"\x' . implode('\x', str_split(bin2hex($param), 2)) . '"';
            }

            return $json;
        }, $params)) . ']';
    }

    /**
     * @param string $wrapperClass
     *
     * @return CDatabase_Exception
     */
    public static function invalidWrapperClass($wrapperClass) {
        return new self("The given 'wrapperClass' " . $wrapperClass . ' has to be a '
                . "subtype of \Doctrine\DBAL\Connection.");
    }

    /**
     * @param string $driverClass
     *
     * @return CDatabase_Exception
     */
    public static function invalidDriverClass($driverClass) {
        return new self("The given 'driverClass' " . $driverClass . ' has to implement the '
                . "\Doctrine\DBAL\Driver interface.");
    }

    /**
     * @param string $tableName
     *
     * @return CDatabase_Exception
     */
    public static function invalidTableName($tableName) {
        return new self('Invalid table name specified: ' . $tableName);
    }

    /**
     * @param string $tableName
     *
     * @return CDatabase_Exception
     */
    public static function noColumnsSpecifiedForTable($tableName) {
        return new self('No columns specified for table ' . $tableName);
    }

    /**
     * @return CDatabase_Exception
     */
    public static function limitOffsetInvalid() {
        return new self('Invalid Offset in Limit Query, it has to be larger than or equal to 0.');
    }

    /**
     * @param string $name
     *
     * @return CDatabase_Exception
     */
    public static function typeExists($name) {
        return new self('Type ' . $name . ' already exists.');
    }

    /**
     * @param string $name
     *
     * @return CDatabase_Exception
     */
    public static function unknownColumnType($name) {
        return new self('Unknown column type "' . $name . '" requested');
    }

    /**
     * @param string $name
     *
     * @return CDatabase_Exception
     */
    public static function typeNotFound($name) {
        return new self(c::__('database.type_not_found', ['type' => $name]));
    }

    /**
     * @param string $dsn
     *
     * @return CDatabase_Exception
     */
    public static function invalidDsn($dsn) {
        return new self(c::__('database.invalid_dsn', ['dsn' => $dsn]));
    }

    /**
     * @param string $table
     *
     * @return CDatabase_Exception
     */
    public static function tableNotFound($table) {
        return new self(c::__('database.table_not_found', ['table' => $table]));
    }

    /**
     * @param string $error
     *
     * @return CDatabase_Exception_QueryException
     */
    public static function queryException($error) {
        return new CDatabase_Exception_QueryException(c::__('database.sql_error', ['error' => $error]));
    }

    /**
     * @param string $error
     *
     * @return CDatabase_Exception_ConnectionException
     */
    public static function connectionException($error) {
        return new CDatabase_Exception_ConnectionException(c::__('database.connection_error', ['error' => $error]));
    }
}
