<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:46:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDatabase_Driver_AbstractMysql extends CDatabase_Driver implements CDatabase_Driver_VersionAwarePlatformInterface, CDatabase_Driver_ServerInfoAwareInterface {

    /**
     * {@inheritdoc}
     */
    public function getDatabase(CDatabase $db) {
        $params = $db->config();

        $dbname = carr::path($params, 'connection.database');
        if ($dbname == null) {
            $dbname = $conn->query('SELECT DATABASE()')->fetchColumn();
        }
        return $dbname;
    }

    /**
     * {@inheritdoc}
     * @return CDatabase_Platform_Mysql
     */
    public function getDatabasePlatform() {
        return new CDatabase_Platform_Mysql();
    }

    /**
     * {@inheritdoc}
     *
     * @throws DBALException
     */
    public function createDatabasePlatformForVersion($version) {
        $mariadb = false !== stripos($version, 'mariadb');
        if ($mariadb && version_compare($this->getMariaDbMysqlVersionNumber($version), '10.2.7', '>=')) {
            return new CDatabase_Platform_MariaDb1027();
        }

        if (!$mariadb) {
            $oracleMysqlVersion = $this->getOracleMysqlVersionNumber($version);
            if (version_compare($oracleMysqlVersion, '8', '>=')) {
                return new CDatabase_Platform_MySQL80();
            }
            if (version_compare($oracleMysqlVersion, '5.7.9', '>=')) {
                return new CDatabase_Platform_MySQL57();
            }
        }

        return $this->getDatabasePlatform();
    }

    /**
     * Detect MariaDB server version, including hack for some mariadb distributions
     * that starts with the prefix '5.5.5-'
     *
     * @param string $versionString Version string as returned by mariadb server, i.e. '5.5.5-Mariadb-10.0.8-xenial'
     * @throws CDatabase_Exception
     */
    private function getMariaDbMysqlVersionNumber($versionString) {
        if (!preg_match(
                        '/^(?:5\.5\.5-)?(mariadb-)?(?P<major>\d+)\.(?P<minor>\d+)\.(?P<patch>\d+)/i', $versionString, $versionParts
                )) {
            throw CDatabase_Exception::invalidPlatformVersionSpecified(
                    $versionString, '^(?:5\.5\.5-)?(mariadb-)?<major_version>.<minor_version>.<patch_version>'
            );
        }

        return $versionParts['major'] . '.' . $versionParts['minor'] . '.' . $versionParts['patch'];
    }

    /**
     * Get a normalized 'version number' from the server string
     * returned by Oracle MySQL servers.
     *
     * @param string $versionString Version string returned by the driver, i.e. '5.7.10'
     * @throws DBALException
     */
    private function getOracleMysqlVersionNumber($versionString) {
        if (!preg_match(
                        '/^(?P<major>\d+)(?:\.(?P<minor>\d+)(?:\.(?P<patch>\d+))?)?/', $versionString, $versionParts
                )) {
            throw CDatabase_Exception::invalidPlatformVersionSpecified(
                    $versionString, '<major_version>.<minor_version>.<patch_version>'
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

}
