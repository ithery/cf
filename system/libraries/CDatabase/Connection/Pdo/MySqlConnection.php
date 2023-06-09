<?php

class CDatabase_Connection_Pdo_MySqlConnection extends CDatabase_Connection implements CDatabase_Contract_VersionAwarePlatformInterface, CDatabase_Driver_ServerInfoAwareInterface {
    /**
     * Escape a binary value for safe SQL embedding.
     *
     * @param string $value
     *
     * @return string
     */
    protected function escapeBinary($value) {
        $hex = bin2hex($value);

        return "x'{$hex}'";
    }

    /**
     * Determine if the connected database is a MariaDB database.
     *
     * @return bool
     */
    public function isMaria() {
        return cstr::contains($this->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), 'MariaDB');
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \CDatabase_Query_Grammar_MySqlGrammar
     */
    protected function getDefaultQueryGrammar() {
        ($grammar = new CDatabase_Query_Grammar_MySqlGrammar())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \CDatabase_Schema_Builder_MySqlBuilder
     */
    public function getSchemaBuilder() {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new CDatabase_Schema_Builder_MySqlBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \CDatabase_Schema_Grammar_MySqlGrammar
     */
    protected function getDefaultSchemaGrammar() {
        ($grammar = new CDatabase_Schema_Grammar_MySqlGrammar())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get the schema state for the connection.
     *
     * @param null|callable $processFactory
     *
     * @return \CDatabase_Schema_SchemaState_MySqlSchemaState
     */
    public function getSchemaState(callable $processFactory = null) {
        return new CDatabase_Schema_SchemaState_MySqlSchemaState($this, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \CDatabase_Query_Processor_MySqlProcessor
     */
    protected function getDefaultPostProcessor() {
        return new CDatabase_Query_Processor_MySqlProcessor();
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \CDatabase_Doctrine_Driver_MySqlDriver
     */
    protected function getDoctrineDriver() {
        return new CDatabase_Doctrine_Driver_MySqlDriver();
    }

    /**
     * @inheritdoc
     *
     * @return CDatabase_Schema_Manager_Mysql
     */
    public function getSchemaManager() {
        return new CDatabase_Schema_Manager_Mysql($this);
    }

    /**
     * @inheritdoc
     *
     * @throws CDatabase_Exception
     */
    public function createDatabasePlatformForVersion($version) {
        $mariadb = false !== stripos($version, 'mariadb');
        if ($mariadb && version_compare($this->getMariaDbMysqlVersionNumber($version), '10.2.7', '>=')) {
            return new CDatabase_Platform_MariaDb1027();
        }

        if (!$mariadb) {
            $oracleMysqlVersion = $this->getOracleMysqlVersionNumber($version);
            if (version_compare($oracleMysqlVersion, '8', '>=')) {
                return new CDatabase_Platform_MySql80();
            }
            if (version_compare($oracleMysqlVersion, '5.7.9', '>=')) {
                return new CDatabase_Platform_MySql57();
            }
        }

        return $this->getDefaultDatabasePlatform();
    }

    /**
     * Detect MariaDB server version, including hack for some mariadb distributions
     * that starts with the prefix '5.5.5-'.
     *
     * @param string $versionString Version string as returned by mariadb server, i.e. '5.5.5-Mariadb-10.0.8-xenial'
     *
     * @throws CDatabase_Exception
     */
    private function getMariaDbMysqlVersionNumber($versionString) {
        if (!preg_match(
            '/^(?:5\.5\.5-)?(mariadb-)?(?P<major>\d+)\.(?P<minor>\d+)\.(?P<patch>\d+)/i',
            $versionString,
            $versionParts
        )
        ) {
            throw CDatabase_Exception::invalidPlatformVersionSpecified(
                $versionString,
                '^(?:5\.5\.5-)?(mariadb-)?<major_version>.<minor_version>.<patch_version>'
            );
        }

        return $versionParts['major'] . '.' . $versionParts['minor'] . '.' . $versionParts['patch'];
    }

    /**
     * Get a normalized 'version number' from the server string
     * returned by Oracle MySQL servers.
     *
     * @param string $versionString Version string returned by the driver, i.e. '5.7.10'
     *
     * @throws CDatabase_Exception
     */
    private function getOracleMysqlVersionNumber($versionString) {
        if (!preg_match(
            '/^(?P<major>\d+)(?:\.(?P<minor>\d+)(?:\.(?P<patch>\d+))?)?/',
            $versionString,
            $versionParts
        )
        ) {
            throw CDatabase_Exception::invalidPlatformVersionSpecified(
                $versionString,
                '<major_version>.<minor_version>.<patch_version>'
            );
        }
        $majorVersion = $versionParts['major'];
        $minorVersion = isset($versionParts['minor']) ? $versionParts['minor'] : 0;
        $patchVersion = isset($versionParts['patch']) ? $versionParts['patch'] : null;
        if ('5' === $majorVersion && '7' === $minorVersion && null === $patchVersion) {
            $patchVersion = '9';
        }

        return $majorVersion . '.' . $minorVersion . '.' . $patchVersion;
    }

    /**
     * @inheritdoc
     *
     * @return CDatabase_Platform_MySql
     */
    public function getDefaultDatabasePlatform() {
        return new CDatabase_Platform_MySql();
    }

    public function requiresQueryForServerVersion() {
        return false;
    }
}
